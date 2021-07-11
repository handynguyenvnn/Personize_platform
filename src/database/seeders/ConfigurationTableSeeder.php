<?php

namespace Database\Seeders;

use App\Consts;
use App\Models\Configuration;
use Illuminate\Database\Seeder;

class ConfigurationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Configuration::truncate();
        $configurations = [
            [
                'key' => Consts::WITHDRAW_SETTINGS_POINT_RATE,
                'value' => '1.1',
                'description' => 'Point rate for withdrawals. Eg. 1.1 -> 1000 points = ¥1100'
            ],
            [
                'key' => Consts::WITHDRAW_SETTINGS_TRANSACTION_FEE_PERCENTAGE,
                'value' => '0.3',
                'description' => 'Default transaction fee charged for withdrawals'
            ],
            [
                'key' => Consts::WITHDRAW_SETTINGS_TRANSFER_FEE,
                'value' => '250',
                'description' => 'Default transfer fee charged for bank transfers'
            ]
        ];
        foreach ($configurations as $configuration) {
            Configuration::create($configuration);
        }
    }
}
