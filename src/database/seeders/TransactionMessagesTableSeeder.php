<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionMessagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('transaction_messages')->insert([

            [
                'id' => 1,
                'transaction_id' => 1,
                'user_id' => 2,
                'body' => '購入希望です。',
                'image_path' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'transaction_id' => 1,
                'user_id' => 1,
                'body' => 'ありがとうございます。対応します。',
                'image_path' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
