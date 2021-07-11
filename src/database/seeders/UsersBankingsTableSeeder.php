<?php

namespace Database\Seeders;

use App\Consts;
use App\Models\UserBank;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


class UsersBankingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        UserBank::truncate();
        $bankDetails = [
            [
                'user_id' => Consts::ROOT_ADMIN_ID,
                'bank_account_holder' => 'NN養成塾　田坂　耕太郎',
                'bank_name' => '三井住友銀行',
                'branch_name' => '岡山支店（651）',
                'bank_account_number' => '7350882',
                'bank_account_type' => '普通'
            ],
        ];
        foreach ($bankDetails as $bankDetail) {
            UserBank::create($bankDetail);
        }
    }
}
