<?php

namespace App\Services;

use App\BigNumber;
use App\Consts;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;

class UserService
{
    public function __construct()
    {
        //
    }

    public function addMoreBalance($userId, $amount, $currency = Consts::CURRENCY_POINT)
    {
        $this->updateBalance($userId, Consts::TRUE, $amount, $currency);
    }

    public function subtractBalance($userId, $amount, $currency = Consts::CURRENCY_POINT)
    {
        $this->updateBalance($userId, Consts::FALSE, $amount, $currency);
    }

    private function updateBalance($userId, $isAddition, $amount, $currency = Consts::CURRENCY_POINT)
    {
        $user = $this->getUserBalanceAndLock($userId);
        $newBalance = BigNumber::new ($user->balance)->sub($amount)->toString();
        if ($isAddition) {
            $newBalance = BigNumber::new ($user->balance)->add($amount)->toString();
        }
        if (BigNumber::new ($newBalance)->comp(0) < 0) {
            throw new Exception(__('exceptions.balance_negative'));
        }
        Log::debug("newBalance" . $newBalance);
        // logger()->error($newBalance);
        $user->balance = $newBalance;
        $user->save();
        return $user;
    }

    public function getUserBalanceAndLock($userId, $currency = Consts::CURRENCY_POINT)
    {
        $user = User::where('id', $userId)
            ->lockForUpdate()
            ->first();

        if (empty($user)) {
            throw new Exception(__('exceptions.not_exists_user'));
        }

        return $user;
    }
}
