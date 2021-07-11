<?php

namespace App\Services;

use App\Consts;
use Exception;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Stripe\WebhookEndpoint;

class StripeService
{

    public function __construct()
    {
        self::setApiKey();
    }

    public function createCheckoutSession($input)
    {
        try {
            $appUrl = env('WEB_APP_URL', 'http://localhost');
            $transactionId = $input['transaction_id'];

            return Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => $input['currency'] ?? Consts::CURRENCY_JPY,
                            'product_data' => [
                                'name' => $input['name'],
                                'images' => [$input['image']],
                            ],
                            'unit_amount' => $input['amount'],
                        ],
                        'quantity' => 1,
                    ],
                ],
                'mode' => 'payment',
                'success_url' => "{$appUrl}/pay/payment-success?id={$transactionId}",
                'cancel_url' => "{$appUrl}/pay/payment-failed?id={$transactionId}",
            ]);
        } catch (Exception $ex) {
            $this->handleStripeException($ex);
        }
    }

    private function handleStripeException($ex)
    {
        logger()->error($ex);
        logger()->error('=======Stripe errors======: ', [$ex->getHttpBody()]);
        throw new Exception($ex);
    }

    public function registerWebhook()
    {
        $this->registerWebhookDeposit();
    }

    private function registerWebhookDeposit()
    {
        $enabledEvents = [
            Consts::STRIPE_WEBHOOK_EVENT_ASYNC_PAYMENT_FAILED,
            Consts::STRIPE_WEBHOOK_EVENT_ASYNC_PAYMENT_SUCCEEDED,
            Consts::STRIPE_WEBHOOK_EVENT_PAYMENT_COMPLETED,
        ];

        $appUrl = env('APP_URL', 'http://localhost');
        WebhookEndpoint::create([
            'url' => "{$appUrl}/api/stripe/web-hook/deposit",
            'enabled_events' => $enabledEvents,
        ]);
    }

    private function setApiKey()
    {
        $apiKey = env('SECRET_STRIPE_API_KEY');
        // if (empty($apiKey)) {
        //     throw new Exception(__('exceptions.stripe_api_key_invalid'));
        // }
        Stripe::setApiKey($apiKey);
    }
}
