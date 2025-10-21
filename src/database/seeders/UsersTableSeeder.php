<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run():void
    {
        DB::table('users')->insert([
            [
                'id' => 1,
                'name' => '徳川家康',
                'email' => 'test@example.com',
                'password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => '小野妹子',
                'email' => 'ono@example.com',
                'password' => Hash::make('password1234'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
