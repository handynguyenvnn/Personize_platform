<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackagePurchasesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //point_purchase
        DB::table('package_purchases')->truncate();
        DB::table('package_purchases')->insert([
            [
                'id' => 1,
                'points' => 500,
                'value' => 550,
                'name' => 'Bronze Package',
                'currency' => 'jpy',
                'cover' => "point/package1.svg",
                'payment_method' => 'stripe',
            ],
            [
                'id' => 2,
                'points' => 1000,
                'value' => 1100,
                'name' => 'Silver Package',
                'currency' => 'jpy',
                'cover' => "point/package2.svg",
                'payment_method' => 'stripe',

            ],
            [
                'id' => 3,
                'points' => 3000,
                'value' => 3300,
                'name' => 'Super Silver Package',

                'currency' => 'jpy',
                'cover' => "point/package3.svg",
                'payment_method' => 'stripe',

            ],
            [
                'id' => 4,
                'points' => 5000,
                'value' => 5500,
                'name' => 'Gold Package',
                'currency' => 'jpy',
                'cover' => "point/package4.svg",
                'payment_method' => 'stripe',

            ],
            [
                'id' => 5,
                'points' => 10000,
                'value' => 11000,
                'name' => 'Super Gold Package',
                'currency' => 'jpy',
                'cover' => "point/package5.svg",
                'payment_method' => 'stripe',

            ],
            [
                'id' => 6,
                'points' => 30000,
                'value' => 33000,
                'name' => 'Diamond Package',
                'currency' => 'jpy',
                'cover' => "point/package6.svg",
                'payment_method' => 'stripe',

            ],
            [
                'id' => 7,
                'points' => 50000,
                'value' => 55000,
                'name' => 'Super Diamond Package',
                'currency' => 'jpy',
                'cover' => "point/package7.svg",
                'payment_method' => 'stripe',

            ],
        ]);
    }
}
