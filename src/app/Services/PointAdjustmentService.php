<?php

namespace App\Services;

use App\Consts;
use App\Models\PointAdjustment;
use App\Services\TransactionService;
use App\Services\UserService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PointAdjustmentService {
    private $transactionService;
    private $userService;

    public function __construct() {
        $this->transactionService = new TransactionService();
        $this->userService = new UserService();
    }

    public function getModel() {
        return PointAdjustment::class;
    }

    public function adjustPoints($user_id, $points, $reason) {
        DB::beginTransaction();
        try {
            // create a point adjustment transaction
            $transaction = $this->transactionService->addTransactions($user_id, -$points, Consts::TRANSACTION_TYPE_ADJUSTMENT);
            // adjust the balance of the user
            $this->userService->subtractBalance($user_id, $points);
            // create new row in point point adjustments table
            $this->createPointAdjustment($transaction->id, $user_id, $points, $reason);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function createPointAdjustment($transaction_id, $user_id, $points, $reason) {
        $pointAdjustment = new PointAdjustment();
        $pointAdjustment->user_id = $user_id;
        $pointAdjustment->transactions_id = $transaction_id;
        $pointAdjustment->points = $points;
        $pointAdjustment->reason = $reason;
        $pointAdjustment->save();

        return $pointAdjustment;
    }
}
