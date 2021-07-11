<?php

namespace App\Services;

use App\Consts;
use App\Models\Event;
use App\Models\EventPayment;
use App\Services\TransactionService;
use App\Services\UserService;

class EventPaymentService
{
    private $transactionService;
    private $userService;

    public function __construct()
    {

        $this->transactionService = new TransactionService();
        $this->userService = new UserService();
    }
    public function getModel()
    {
        return EventPayment::class;
    }
    public function makePaymentEvent($request)
    {
        $event_check = Event::query()->withCount('userSubscribeEvent')->withCount('userLiveEvent')->findOrFail($request->event_id);
        $current_user = auth()->user();

        $this->userService->addMoreBalance($event_check->user_id, $event_check->points);
        $this->userService->subtractBalance($current_user->id, $event_check->points);

        $transactions = $this->transactionService->addTransactions($current_user->id, -$event_check->points, Consts::TRANSACTION_TYPE_PAY);
        $target_transactions = $this->transactionService->addTransactions($event_check->user_id, $event_check->points, Consts::TRANSACTION_TYPE_EARN);
        $this->createEventPayment($transactions->id, $target_transactions->id, $current_user->id, $event_check->user_id, $event_check->points, $event_check->id);
    }
    public function createEventPayment($transactions_id, $target_transactions_id, $user_id, $target_user_id, $points, $event_id)
    {
        $eventPayment = new EventPayment();
        $eventPayment->transactions_id = $transactions_id;
        $eventPayment->target_transactions_id = $target_transactions_id;
        $eventPayment->user_id = $user_id;
        $eventPayment->target_user_id = $target_user_id;
        $eventPayment->points = $points;
        $eventPayment->event_id = $event_id;
        $eventPayment->save();
        return $eventPayment;
    }

}
