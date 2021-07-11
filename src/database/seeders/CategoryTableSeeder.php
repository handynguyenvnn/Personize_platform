<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::truncate();
        $categories = [
            [
                'name' => '全て',
                'color' => '#025222',
                'position' => 1,
                'icon' => 'icons/time.svg',
            ],
            [
                'name' => '公式',
                'color' => '#000000',
                'position' => 2,
                'is_admin' => Category::CATEGORY_ADMIN,
                'icon' => 'icons/time.svg',
            ],
            [
                'name' => 'タレント',
                'color' => '#FF0000',
                'position' => 3,
                'icon' => 'icons/disc.svg',

            ],
            [
                'name' => 'ミュージック',
                'color' => '#ED9DC2',
                'position' => 4,
                'icon' => 'icons/music-note.svg',

            ],
            [
                'name' => 'アニメ',
                'subTitle' => '声優',
                'color' => '#EC7A00',
                'position' => 5,
                'icon' => 'icons/mic.svg',

            ],
            [
                'name' => '勉強',
                'color' => '#00EC48',
                'position' => 6,
                'icon' => 'icons/readme.svg',

            ],
            [
                'name' => 'ゲーマー',
                'color' => '#00E4EC',
                'position' => 7,
                'icon' => 'icons/games.svg',

            ],
            [
                'name' => 'ビジネス',
                'color' => '#CC00FF',
                'position' => 8,
                'icon' => 'icons/work-alt.svg',

            ],
            [
                'name' => 'その他',
                'color' => '#B5B5B5',
                'position' => 9,
                'icon' => 'icons/layout-grid-small.svg',

            ],
        ];
        foreach ($categories as $category) {
            Category::create($category);

        }
    }
}
