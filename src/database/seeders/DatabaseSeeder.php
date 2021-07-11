<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //disable foreign key check for this connection before running seeders
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // $this->call(UsersTableSeeder::class);
        // $this->call(UsersBankingsTableSeeder::class);
        // $this->call(CategoryTableSeeder::class);
        // // $this->call(EventTableSeeder::class);
        // $this->call(ConfigurationTableSeeder::class);
        $this->call(HashtagTableSeeder::class);
        // $this->call(PackagePurchasesTableSeeder::class);
        // $this->call(CountriesTableSeeder::class);
        // $this->call(PrefecturesTableSeeder::class);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
