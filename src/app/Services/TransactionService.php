<?php

namespace App\Services;

use App\Models\Transactions;

class TransactionService
{
    public function __construct()
    {
        //
    }
    public function addTransactions($user_id, $points, $type)
    {
        $transactions = new Transactions;
        $transactions->user_id = $user_id;
        $transactions->points = $points;
        $transactions->type = $type;
        $transactions->save();
        return $transactions;
    }

}
