<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConditionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('conditions')->insert([
            [
                'condition' => '良好',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'condition' => '目立った傷や汚れなし',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'condition' => 'やや傷や汚れあり',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'condition' => '状態が悪い',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
