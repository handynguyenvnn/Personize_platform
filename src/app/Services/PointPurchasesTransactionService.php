<?php

namespace App\Services;

use App\BigNumber;
use App\Consts;
use App\Models\Package;
use App\Models\PointPurchases;
use App\Services\StripeService;
use App\Services\TransactionService;
use App\Services\UserService;
use Auth;
use DB;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PointPurchasesTransactionService
{
    private $stripeService;
    private $userService;
    private $transactionService;

    public function __construct()
    {
        $this->stripeService = new StripeService();
        $this->userService = new UserService();
        $this->transactionService = new TransactionService();

    }

    public function deposit($input)
    {
        $package = Package::find($input['package_id']);
        if (!$package) {
            throw new Exception(__('exceptions.package_not_exists'));
        }

        $transaction = $this->createTransaction(Auth::id(), $package);
        return $this->depositStripe($transaction);
        // $input['currency'] = Consts::CURRENCY_POINT;
        // $input['real_currency'] = Consts::CURRENCY_JPY;
        // $input['type'] = Consts::TRANSACTION_TYPE_DEPOSIT;
        // $input['payment_type'] = Consts::PAYMENT_TYPE_CREDIT_CARD;

        // $transaction = $this->createTransaction(Auth::id(), $input);
        // return $this->depositStripe($transaction);
    }

    private function depositStripe($transaction)
    {
        $input['amount'] = $transaction->value;
        $input['currency'] = $transaction->package->currency;
        $input['transaction_id'] = $transaction->id;
        $input['image'] = $transaction->package->cover;
        $input['name'] = $transaction->package->name;
        Log::debug($input);
        $session = $this->stripeService->createCheckoutSession($input);

        if ($session) {
            $transaction->stripe_transactions_id = $session['id'];
            $transaction->status = Consts::TRANSACTION_STATUS_CREATED;
            $transaction->save();
        }
        return $session;
    }

    private function createTransaction($userId, $package, $transactionId = null, $memo = null)
    {
        $currentMillis = $this->currentMilliseconds();
        $transaction = new PointPurchases();
        $transaction->user_id = $userId;
        $transaction->stripe_transactions_id = $transactionId ? $transactionId : Str::uuid()->toString();
        // $transaction->currency = Consts::CURRENCY_POINT;
        $transaction->payment_type = Consts::PAYMENT_TYPE_CREDIT_CARD;
        $transaction->type = Consts::TRANSACTION_TYPE_DEPOSIT;
        $transaction->status = Consts::TRANSACTION_STATUS_CREATING;
        // $transaction->memo = $memo;
        // $transaction->created_at = $currentMillis;
        // $transaction->updated_at = $currentMillis;
        // $transaction->real_amount = $package['value'];
        // $transaction->real_currency = $package['currency'];
        $transaction->points = $package['points'];
        $transaction->value = $package['value'];
        $transaction->package_id = $package['id'];
        Log::debug("transaction" . $transaction->stripe_transactions_id);

        // if (array_key_exists('real_amount', $input)) {
        //     $transaction->real_amount = $input['real_amount'];
        // }
        // if (array_key_exists('real_currency', $input)) {
        //     $transaction->real_currency = $input['real_currency'];
        // }
        // if (array_key_exists('amount', $input)) {
        //     $transaction->amount = $input['amount'];
        // }
        // if (array_key_exists('offer_id', $input)) {
        //     $transaction->offer_id = $input['offer_id'];
        //     $offer = Offer::find($input['offer_id']);
        //     if (!$offer) {
        //         throw new Exception(__('exceptions.offer_not_exists'));
        //     }
        //     $transaction->amount = $offer->point;
        //     $transaction->real_amount = $this->calculateRealAmount($offer->point);
        // }

        $transaction->save();

        return $transaction;
    }

    private function calculateRealAmount($amount)
    {
        list($source, $target) = explode(Consts::CHAR_COLON, Consts::PAYMENT_RATE_JPY_POINT);

        return BigNumber::new ($amount)->mul($target)->div($source)->toString();
    }

    public function executeDeposit($id)
    {
        $transaction = $this->getTransactionById($id);

        $transaction->status = Consts::TRANSACTION_STATUS_EXECUTING;
        $transaction->save();

        return $transaction;
    }

    public function cancelDeposit($id)
    {
        $transaction = $this->getTransactionById($id);

        $transaction->status = Consts::TRANSACTION_STATUS_CANCEL;
        $transaction->save();

        return $transaction;
    }

    private function getTransactionById($id)
    {
        $transaction = PointPurchases::where('id', $id)
            ->where('payment_type', Consts::PAYMENT_TYPE_CREDIT_CARD)
            ->where('type', Consts::TRANSACTION_TYPE_DEPOSIT)
            ->whereIn('status', [Consts::TRANSACTION_STATUS_CREATING, Consts::TRANSACTION_STATUS_CREATED])
            ->first();

        if (!$transaction) {
            throw new Exception(__('exceptions.transaction_invalid', ['id' => $transactionId]));
        }
        return $transaction;
    }

    public function depositStripeWebHook($session)
    {
        Log::debug('=====Information session deposit from stripe webhook=====: ' . json_encode($session));
        $this->handleStripeWebhook($session['data']['object']['id'], $session['type'], Consts::TRANSACTION_TYPE_DEPOSIT, json_encode($session));
    }

    private function handleStripeWebhook($stripe_transactions_id, $transactionStatus, $type, $errorMsg = null)
    {
        Log::debug("transactions:" . $stripe_transactions_id);
        $transaction = PointPurchases::where('stripe_transactions_id', $stripe_transactions_id)
            ->first();
        DB::beginTransaction();
        try {
            if (!$transaction) {
                throw new Exception(__('exceptions.transaction_invalid', ['id' => $stripe_transactions_id]));
            }

            switch ($transactionStatus) {
                case Consts::STRIPE_WEBHOOK_EVENT_ASYNC_PAYMENT_FAILED:
                    $transaction->status = Consts::TRANSACTION_STATUS_FAILED;
                    $transaction->error_detail = $errorMsg;
                    break;
                case Consts::STRIPE_WEBHOOK_EVENT_ASYNC_PAYMENT_SUCCEEDED:
                case Consts::STRIPE_WEBHOOK_EVENT_PAYMENT_COMPLETED:
                    Log::debug("payment_completed");
                    $transaction->status = Consts::TRANSACTION_STATUS_SUCCESS;
                    $trans = $this->transactionService->addTransactions($transaction->user_id, $transaction->points, Consts::TRANSACTION_TYPE_DEPOSIT);
                    if (!$trans) {
                        throw new Exception(__('exceptions.transaction_invalid', ['id' => $stripe_transactions_id]));
                    }
                    Log::debug("transssss" . json_encode($trans));
                    $transaction->transactions_id = $trans->id;
                    $this->userService->addMoreBalance($transaction->user_id, $transaction->points);
                    break;
                default:
                    // Unexpected event type
                    logger('Unexpected event type stripe: ' . $event);
                    break;
            }
            $transaction->save();

            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();
            logger()->error('=======Transaction errors======: ', [$ex]);
        }
    }

    public function listPackage()
    {
        return Package::all();
    }

    private function currentMilliseconds()
    {
        return round(microtime(true) * 1000);
    }

    private function generateRandomString($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
    {
        $pieces = [];
        $max = strlen($keyspace) - 1;
        for ($i = 0; $i < $length; ++$i) {
            $pieces[] = $keyspace[random_int(0, $max)];
        }

        return implode('', $pieces);
    }
}
