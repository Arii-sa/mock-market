<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('transactions')->insert([
            [
                'id' => 1,
                'item_id' => 1,
                'buyer_id' => 2,
                'seller_id' => 1,
                'status' => 'trading',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'item_id' => 6,
                'buyer_id' => 1,
                'seller_id' => 2,
                'status' => 'trading',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
