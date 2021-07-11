<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::truncate();
        $users = [
            [
                'name' => 'Admin',
                'nick_name' => 'Admin',
                'email' => 'admin-showroom@gmail.com',
                'password' => Hash::make('12345678'),
                'role' => User::USER_ROLE_ADMIN,
                'email_verified_at' => now()
          ],
            [
                'name' => 'Normal',
                'nick_name' => 'Normal',
                'email' => 'normal-showroom@gmail.com',
                'password' => Hash::make('12345678'),
                'email_verified_at' => now()
            ]
        ];
        foreach ($users as $user) {
            User::create($user);
        }
    }
}
