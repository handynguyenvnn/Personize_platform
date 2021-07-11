<?php

namespace App\Services;

use App\Consts;
use App\Models\Configuration;
use App\Models\WithdrawRequest;
use App\Models\WithdrawTransaction;
use App\Services\TransactionService;
use App\Services\UserService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WithdrawTransactionService {
    private $transactionService;
    private $userService;

    public function __construct() {
        $this->transactionService = new TransactionService();
        $this->userService = new UserService();
    }

    public function getModel() {
        return WithdrawTransaction::class;
    }

    public function withdraw($withdrawRequestId, $data) {
        $withdrawRequest = WithdrawRequest::findOrFail($withdrawRequestId);
        $user = $withdrawRequest->user;

        $pointRate = floatval(Configuration::where('key', Consts::WITHDRAW_SETTINGS_POINT_RATE)->first()['value']);
        // get default fee settings or take from $data if available
        $transactionFeePercentage = isset($data['transactionFee']) ?
            floatval($data['transactionFee']) :
            floatval(Configuration::where('key', Consts::WITHDRAW_SETTINGS_TRANSACTION_FEE_PERCENTAGE)->first()['value']);
        $transferFee = isset($data['transferFee']) ?
            floatval($data['transferFee']) :
            floatval(Configuration::where('key', Consts::WITHDRAW_SETTINGS_TRANSFER_FEE)->first()['value']);

        // calculate Yen values
        $amountBeforeFees = $withdrawRequest->point * $pointRate;
        $transactionFee = $amountBeforeFees * $transactionFeePercentage;
        $amountAfterFees = $amountBeforeFees - $transactionFee - $transferFee;

        DB::beginTransaction();
        try {
            $transactions = $this->transactionService->addTransactions($user->id, -$withdrawRequest->point, Consts::TRANSACTION_TYPE_WITHDRAW);
            $this->userService->subtractBalance($user->id, $withdrawRequest->point);
            $this->createWithdrawTransaction(
                $transactions->id, $user->id, $withdrawRequest->point, $withdrawRequest->id, 
                $transactionFee, $transferFee, $amountBeforeFees, $amountAfterFees
            );
            // update withdraw request's 'amounts' value correctly after considering all fees. also set the new status
            $withdrawRequest->update([
                'amount' => $amountAfterFees,
                'status' => $data['status'],
                'is_read' => $data['is_read']
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function createWithdrawTransaction(
        $transactions_id, $user_id, $points, $withdraw_request_id, $transaction_fee, $transfer_fee, $amount_before_fees, $amount_after_fees
    ) {
        $withdrawTransaction = new WithdrawTransaction();
        $withdrawTransaction->user_id = $user_id;
        $withdrawTransaction->transactions_id = $transactions_id;
        $withdrawTransaction->withdraw_request_id = $withdraw_request_id;
        $withdrawTransaction->points = $points;
        $withdrawTransaction->transaction_fee = $transaction_fee;
        $withdrawTransaction->transfer_fee = $transfer_fee;
        $withdrawTransaction->amount_before_fees = $amount_before_fees;
        $withdrawTransaction->amount_after_fees = $amount_after_fees;
        $withdrawTransaction->save();

        return $withdrawTransaction;
    }
}
