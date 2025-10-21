<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $items = DB::table('items')->pluck('id'); // 実際の id を全部取得
        $categoryCount = DB::table('categories')->count();

        foreach ($items as $itemId) {
            $randomCategories = collect(range(1, $categoryCount))
                ->random(rand(1, 3)); // 1〜3カテゴリ

            foreach ($randomCategories as $categoryId) {
                DB::table('category_items')->insert([
                    'item_id' => $itemId,
                    'category_id' => $categoryId,
                ]);
            }
        }
    }
}
