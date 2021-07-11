<?php

namespace App\Services;

use App\Consts;
use App\Models\Refund;
use App\Services\TransactionService;
use App\Services\UserService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RefundService {
    private $transactionService;
    private $userService;

    public function __construct() {
        $this->transactionService = new TransactionService();
        $this->userService = new UserService();
    }

    public function getModel() {
        return Refund::class;
    }

    public function refund($user_id, $points, $reason) {
        DB::beginTransaction();
        try {
            // create a point adjustment transaction
            $transaction = $this->transactionService->addTransactions($user_id, $points, Consts::TRANSACTION_TYPE_REFUND);
            // adjust the balance of the user
            $this->userService->addMoreBalance($user_id, $points);
            // create new row in point point adjustments table
            $this->createRefund($transaction->id, $user_id, $points, $reason);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function createRefund($transaction_id, $user_id, $points, $reason) {
        $refund = new Refund();
        $refund->user_id = $user_id;
        $refund->transactions_id = $transaction_id;
        $refund->points = $points;
        $refund->reason = $reason;
        $refund->save();

        return $refund;
    }
}
