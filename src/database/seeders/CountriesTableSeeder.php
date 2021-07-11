<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


class CountriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Country::truncate();
        $data = [
            [
                'name_jp' => '日本',
                'name_en' => 'Japan',
            ],
            [
                'name_jp' => '海外',
                'name_en' => 'Foreign',
            ],
            [
                'name_jp' => 'その他',
                'name_en' => 'Other',
            ],
        ];
        foreach ($data as $item) {
            Country::create($item);
        }
    }
}
