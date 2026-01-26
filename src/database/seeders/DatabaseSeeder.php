<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        \App\Models\TransactionMessageRead::truncate();
        \App\Models\TransactionMessage::truncate();
        \App\Models\Evaluation::truncate();
        \App\Models\Transaction::truncate();

        \App\Models\CategoryItem::truncate();
        \App\Models\Item::truncate();
        \App\Models\Profile::truncate();
        \App\Models\User::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->call([
            UsersTableSeeder::class,
            ConditionsTableSeeder::class,
            CategoriesTableSeeder::class,
            ItemsTableSeeder::class,
            CategoryItemsTableSeeder::class,
            ProfilesTableSeeder::class,

            TransactionsTableSeeder::class,
            TransactionMessagesTableSeeder::class,
            TransactionMessageReadsTableSeeder::class,
        ]);
    }
}
