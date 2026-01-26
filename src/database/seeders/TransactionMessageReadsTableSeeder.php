<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionMessageReadsTableSeeder extends Seeder
{

    public function run()
    {
        DB::table('transaction_message_reads')->insert([
            [
                'transaction_id' => 1,
                'user_id' => 1,
                'last_read_message_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'transaction_id' => 1,
                'user_id' => 2,
                'last_read_message_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
