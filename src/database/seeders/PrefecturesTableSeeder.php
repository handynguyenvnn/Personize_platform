<?php

namespace Database\Seeders;

use App\Models\Prefecture;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


class PrefecturesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Prefecture::truncate();
        $data = [
            [
              "id" => 1,
              "name_jp" => "北海道",
              "name_en" => "",
              "country_id" => 1
            ],
            [
              "id" => 2,
              "name_jp" => "青森県",
              "name_en" => "",
              "country_id" => 1
            ],
            [
              "id" => 3,
              "name_jp" => "岩手県",
              "name_en" => "",
              "country_id" => 1
            ],
            [
              "id" => 4,
              "name_jp" => "宮城県",
              "name_en" => "",
              "country_id" => 1
            ],
            [
              "id" => 5,
              "name_jp" => "秋田県",
              "name_en" => "",
              "country_id" => 1
            ],
            [
              "id" => 6,
              "name_jp" => "山形県",
              "name_en" => "",
              "country_id" => 1
            ],
            [
              "id" => 7,
              "name_jp" => "福島県",
              "name_en" => "",
              "country_id" => 1
            ],
            [
              "id" => 8,
              "name_jp" => "茨城県",
              "name_en" => "",
              "country_id" => 1
            ],
            [
              "id" => 9,
              "name_jp" => "栃木県",
              "name_en" => "",
              "country_id" => 1
            ],
            [
              "id" => 10,
              "name_jp" => "群馬県",
              "name_en" => "",
              "country_id" => 1
            ],
            [
              "id" => 11,
              "name_jp" => "埼玉県",
              "name_en" => "",
              "country_id" => 1
            ],
            [
              "id" => 12,
              "name_jp" => "千葉県",
              "name_en" => "",
              "country_id" => 1
            ],
            [
              "id" => 13,
              "name_jp" => "東京都",
              "name_en" => "",
              "country_id" => 1
            ],
            [
              "id" => 14,
              "name_jp" => "神奈川県",
              "name_en" => "",
              "country_id" => 1
            ],
            [
              "id" => 15,
              "name_jp" => "新潟県",
              "name_en" => "",
              "country_id" => 1
            ],
            [
              "id" => 16,
              "name_jp" => "富山県",
              "name_en" => "",
              "country_id" => 1
            ],
            [
              "id" => 17,
              "name_jp" => "石川県",
              "name_en" => "",
              "country_id" => 1
            ],
            [
              "id" => 18,
              "name_jp" => "福井県",
              "name_en" => "",
              "country_id" => 1
            ],
            [
              "id" => 19,
              "name_jp" => "山梨県",
              "name_en" => "",
              "country_id" => 1
            ],
            [
              "id" => 20,
              "name_jp" => "長野県",
              "name_en" => "",
              "country_id" => 1
            ],
            [
              "id" => 21,
              "name_jp" => "岐阜県",
              "name_en" => "",
              "country_id" => 1
            ],
            [
              "id" => 22,
              "name_jp" => "静岡県",
              "name_en" => "",
              "country_id" => 1
            ],
            [
              "id" => 23,
              "name_jp" => "愛知県",
              "name_en" => "",
              "country_id" => 1
            ],
            [
              "id" => 24,
              "name_jp" => "三重県",
              "name_en" => "",
              "country_id" => 1
            ],
            [
              "id" => 25,
              "name_jp" => "滋賀県",
              "name_en" => "",
              "country_id" => 1
            ],
            [
              "id" => 26,
              "name_jp" => "京都府",
              "name_en" => "",
              "country_id" => 1
            ],
            [
              "id" => 27,
              "name_jp" => "大阪府",
              "name_en" => "",
              "country_id" => 1
            ],
            [
              "id" => 28,
              "name_jp" => "兵庫県",
              "name_en" => "",
              "country_id" => 1
            ],
            [
              "id" => 29,
              "name_jp" => "奈良県",
              "name_en" => "",
              "country_id" => 1
            ],
            [
              "id" => 30,
              "name_jp" => "和歌山県",
              "name_en" => "",
              "country_id" => 1
            ],
            [
              "id" => 31,
              "name_jp" => "鳥取県",
              "name_en" => "",
              "country_id" => 1
            ],
            [
              "id" => 32,
              "name_jp" => "島根県",
              "name_en" => "",
              "country_id" => 1
            ],
            [
              "id" => 33,
              "name_jp" => "岡山県",
              "name_en" => "",
              "country_id" => 1
            ],
            [
              "id" => 34,
              "name_jp" => "広島県",
              "name_en" => "",
              "country_id" => 1
            ],
            [
              "id" => 35,
              "name_jp" => "山口県",
              "name_en" => "",
              "country_id" => 1
            ],
            [
              "id" => 36,
              "name_jp" => "徳島県",
              "name_en" => "",
              "country_id" => 1
            ],
            [
              "id" => 37,
              "name_jp" => "香川県",
              "name_en" => "",
              "country_id" => 1
            ],
            [
              "id" => 38,
              "name_jp" => "愛媛県",
              "name_en" => "",
              "country_id" => 1
            ],
            [
              "id" => 39,
              "name_jp" => "高知県",
              "name_en" => "",
              "country_id" => 1
            ],
            [
              "id" => 40,
              "name_jp" => "福岡県",
              "name_en" => "",
              "country_id" => 1
            ],
            [
              "id" => 41,
              "name_jp" => "佐賀県",
              "name_en" => "",
              "country_id" => 1
            ],
            [
              "id" => 42,
              "name_jp" => "長崎県",
              "name_en" => "",
              "country_id" => 1
            ],
            [
              "id" => 43,
              "name_jp" => "熊本県",
              "name_en" => "",
              "country_id" => 1
            ],
            [
              "id" => 44,
              "name_jp" => "大分県",
              "name_en" => "",
              "country_id" => 1
            ],
            [
              "id" => 45,
              "name_jp" => "宮崎県",
              "name_en" => "",
              "country_id" => 1
            ],
            [
              "id" => 46,
              "name_jp" => "鹿児島県",
              "name_en" => "",
              "country_id" => 1
            ],
            [
              "id" => 47,
              "name_jp" => "沖縄県",
              "name_en" => "",
              "country_id" => 1
            ]
        ];
        foreach ($data as $item) {
            Prefecture::create($item);
        }
    }
}
