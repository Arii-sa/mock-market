<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProfilesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run():void
    {
        DB::table('profiles')->insert([
            [
                'user_id' => 1,
                'img_url' => 'https://example.com/images/user1.png',
                'postcode' => '123-4567',
                'address' => '東京都渋谷区1-2-3',
                'building' => 'テストマンション101',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'img_url' => 'https://example.com/images/user2.png',
                'postcode' => '987-6543',
                'address' => '大阪府大阪市中央区4-5-6',
                'building' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
