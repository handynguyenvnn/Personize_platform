<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Event::truncate();
        for ($i = 1; $i <= 8; $i++) {
            for ($j = 0; $j < 15; $j++) {
                $data = [
                    'title' => 'Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s',
                    'description' => 'Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s',
                    'type' => mt_rand(1, 2),
                    'link_stream' => 'https://zoom.us/',
                    'time' => date('H:s', strtotime('+' . mt_rand(-1000, 1000) . ' minutes')),
                    'image' => 'seed_image/Rectangle ' . mt_rand(1, 42) . '.png',
                    'image_banner' => 'seed_image/Component ' . mt_rand(1, 7) . '.png',
                    'date' => date('Y-m-d', strtotime('+' . mt_rand(-30, 30) . ' days')),
                    'capacity' => mt_rand(1, 100),
                    'category_id' => $i,
                    'user_id' => mt_rand(1, 2),
                    'points' => 0,
                ];
                Event::create($data);
            }
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    }
}
