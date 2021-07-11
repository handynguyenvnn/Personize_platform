<?php

namespace App;

class Consts
{
    const TRUE = 1;
    const FALSE = 0;

    const CHAR_COLON = ':';

    const CURRENCY_JPY = 'jpy';
    const CURRENCY_POINT = 'point';

    const STRIPE_TRANSACTION_STATUS_SUCCESS = 'succeeded';

    const PAYMENT_SERVICE_TYPE_STRIPE = 'stripe';
    const PAYMENT_SERVICE_TYPE_INTERNAL = 'internal';

    const TRANSACTION_TYPE_DEPOSIT = 'deposit';
    const TRANSACTION_TYPE_PAY = 'pay';
    const TRANSACTION_TYPE_EARN = 'earn';
    const TRANSACTION_TYPE_PACKAGE = 'package';
    const TRANSACTION_TYPE_WITHDRAW = 'withdraw';
    const TRANSACTION_TYPE_GIFT = 'gift';
    const TRANSACTION_TYPE_REFUND = 'refund';
    const TRANSACTION_TYPE_ADJUSTMENT = 'adjustment';

    const TRANSACTION_STATUS_SUCCESS = 'success';
    const TRANSACTION_STATUS_FAILED = 'failed';
    const TRANSACTION_STATUS_CREATING = 'creating';
    const TRANSACTION_STATUS_CREATED = 'created';
    const TRANSACTION_STATUS_EXECUTING = 'executing';
    const TRANSACTION_STATUS_CANCEL = 'cancel';

    const TRANSACTION_MEMO_DEPOSIT_SUCCESS = 'Deposit Successful';

    const STRIPE_WEBHOOK_EVENT_ASYNC_PAYMENT_FAILED = 'checkout.session.async_payment_failed';
    const STRIPE_WEBHOOK_EVENT_ASYNC_PAYMENT_SUCCEEDED = 'checkout.session.async_payment_succeeded';
    const STRIPE_WEBHOOK_EVENT_PAYMENT_COMPLETED = 'checkout.session.completed';

    const PAYMENT_TYPE_CREDIT_CARD = 'credit card';

    const PAYMENT_RATE_JPY_POINT = '1:1';

    const EVENT_STATUS_COMING = 1;
    const EVENT_STATUS_LIVE = 2;
    const EVENT_STATUS_FINISH = 3;
    const EVENT_STATUS_CANCEL = 4;

    const WITHDRAW_REQUEST_STATUS_PENDING = 1;
    const WITHDRAW_REQUEST_STATUS_ACCEPTED = 2;
    const WITHDRAW_REQUEST_STATUS_PAID_OUT = 3;
    const WITHDRAW_REQUEST_STATUS_REJECTED = 4;

    // keys for configuration table (related to withdraw settings)
    const WITHDRAW_SETTINGS_POINT_RATE = 'point_rate';
    const WITHDRAW_SETTINGS_TRANSACTION_FEE_PERCENTAGE = 'transaction_fee_percentage';
    const WITHDRAW_SETTINGS_TRANSFER_FEE = 'transfer_fee';

    const ROOT_ADMIN_ID = 1;
}
