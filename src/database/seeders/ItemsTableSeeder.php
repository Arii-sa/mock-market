<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Enums\ItemStatus;

class ItemsTableSeeder extends Seeder
{
    public function run()
    {
        $images = [
        1 => 'clock.jpg',
        2 => 'disk.png',
        3 => 'onion.png',
        4 => 'shoes.png',
        5 => 'pc.png',
        6 => 'mic.png',
        7 => 'bag.png',
        8 => 'tumbler.png',
        9 => 'coffee.png',
        10 => 'cosme.png',
        ];

        foreach ($images as $id => $fileName) {
            $from = database_path("seeders/images/{$fileName}");
            $to   = "items/{$fileName}";

            if (!Storage::disk('public')->exists($to)) {
                Storage::disk('public')->put($to, File::get($from));
            }
        }

        DB::table('items')->insert([
            [
                'id' => 1,
                'user_id' => 1,
                'condition_id' => 1,
                'name' => '腕時計',
                'price' => 15000,
                'brand' => 'Rolax',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'img_url' => 'items/clock.jpg',
                'status' => ItemStatus::TRADING->value,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'user_id' => 1,
                'condition_id' => 2,
                'name' => 'HDD',
                'price' => 5000,
                'brand' => '西芝',
                'description' => '高速で信頼性の高いハードディスク',
                'img_url' => 'items/disk.png',
                'status' => ItemStatus::AVAILABLE->value,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'user_id' => 1,
                'condition_id' => 3,
                'name' => '玉ねぎ3束',
                'price' => 300,
                'brand' => null,
                'description' => '新鮮な玉ねぎ3束のセット',
                'img_url' => 'items/onion.png',
                'status' => ItemStatus::AVAILABLE->value,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'user_id' => 1,
                'condition_id' => 4,
                'name' => '革靴',
                'price' => 4000,
                'brand' => null,
                'description' => 'クラシックなデザインの革靴',
                'img_url' => 'items/shoes.png',
                'status' => ItemStatus::AVAILABLE->value,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'user_id' => 1,
                'condition_id' => 1,
                'name' => 'ノートPC',
                'price' => 45000,
                'brand' => null,
                'description' => '高性能なノートパソコン',
                'img_url' => 'items/pc.png',
                'status' => ItemStatus::AVAILABLE->value,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'user_id' => 2,
                'condition_id' => 2,
                'name' => 'マイク',
                'price' => 8000,
                'brand' => null,
                'description' => '高音質のレコーディング用マイク',
                'img_url' => 'items/mic.png',
                'status' => ItemStatus::TRADING->value,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'user_id' => 2,
                'condition_id' => 3,
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'brand' => null,
                'description' => 'おしゃれなショルダーバッグ',
                'img_url' => 'items/bag.png',
                'status' => ItemStatus::AVAILABLE->value,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 8,
                'user_id' => 2,
                'condition_id' => 4,
                'name' => 'タンブラー',
                'price' => 500,
                'brand' => null,
                'description' => '使いやすいタンブラー',
                'img_url' => 'items/tumbler.png',
                'status' => ItemStatus::AVAILABLE->value,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 9,
                'user_id' => 2,
                'condition_id' => 1,
                'name' => 'コーヒーミル',
                'price' => 4000,
                'brand' => 'Starbacks',
                'description' => '手動のコーヒーミル',
                'img_url' => 'items/coffee.png',
                'status' => ItemStatus::AVAILABLE->value,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 10,
                'user_id' => 2,
                'condition_id' => 2,
                'name' => 'メイクセット',
                'price' => 2500,
                'brand' => null,
                'description' => '便利なメイクアップセット',
                'img_url' => 'items/cosme.png',
                'status' => ItemStatus::AVAILABLE->value,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
