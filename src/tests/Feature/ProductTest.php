<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Like;
use App\Models\Comment;
use App\Models\Category;
use App\Models\Condition;
use App\Models\SoldItem;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /** @test 全商品を取得できる */
    public function test_items_list()
    {

        $condition = Condition::create([
            'condition' => '新品', // 適当な値
        ]);

        // ユーザーを3人作成
        $loginUser = User::create([
            'name' => 'User1',
            'email' => 'user1@example.com',
            'password' => bcrypt('password'),
        ]);

        $otherUser = User::create([
            'name' => 'User2',
            'email' => 'user2@example.com',
            'password' => bcrypt('password'),
        ]);

        // 商品を作成
        $item1 = Item::create([
            'user_id' => $otherUser->id,
            'condition_id' => $condition->id,
            'name' => '商品1',
            'price' => 1000,
            'brand' => 'ブランドA',
            'description' => '説明1',
            'img_url' => 'test1.jpg',
        ]);

        $item2 = Item::create([
            'user_id' => $otherUser->id,
            'condition_id' => $condition->id,
            'name' => '商品2',
            'price' => 2000,
            'brand' => 'ブランドB',
            'description' => '説明2',
            'img_url' => 'test2.jpg',
        ]);

        $myItem = Item::create([
            'user_id' => $loginUser->id,
            'condition_id' => $condition->id,
            'name' => '自分の商品',
            'price' => 5000,
            'brand' => 'ブランドD',
            'description' => '自分用',
            'img_url' => 'myitem.jpg',
        ]);

        // 購入済み商品を作成
        SoldItem::create([
            'item_id' => $item2->id,
            'user_id' => $loginUser->id,
            'sending_postcode' => '123-4567',
            'sending_address' => '東京都渋谷区',
            'sending_building' => 'テストビル',
        ]);

        // ログインユーザーとして
        $this->actingAs($loginUser);

        $response = $this->get(route('items.index'));

        // 1. すべての商品が表示される（購入済みも含むが自分の商品は除く）
        $response->assertSee($item1->name);
        $response->assertSee($item2->name); // 購入済み
        $response->assertDontSee($myItem->name); // 自分の商品は表示されない

        // 2. 購入済み商品には "Sold" ラベルが表示される
        $response->assertSee('Sold');
    }




    /** @test マイリストタブでいいねした商品のみが表示される */
    public function test_mylist_shows()
    {
        $condition = Condition::create(['condition' => '新品']);

        // ユーザー作成
        $user = User::create([
            'name' => 'User1',
            'email' => 'user1@example.com',
            'password' => bcrypt('password'),
        ]);

        $otherUser = User::create([
            'name' => 'User2',
            'email' => 'user2@example.com',
            'password' => bcrypt('password'),
        ]);

        // 商品作成
        $item1 = Item::create([
            'user_id' => $otherUser->id,
            'condition_id' => $condition->id,
            'name' => '商品A',
            'price' => 1000,
            'brand' => 'ブランドA',
            'description' => '説明A',
            'img_url' => 'a.jpg',
        ]);

        $item2 = Item::create([
            'user_id' => $otherUser->id,
            'condition_id' => $condition->id,
            'name' => '商品B',
            'price' => 2000,
            'brand' => 'ブランドB',
            'description' => '説明B',
            'img_url' => 'b.jpg',
        ]);

        // ログインユーザーが商品1をお気に入りにする
        Like::create([
            'user_id' => $user->id,
            'item_id' => $item1->id,
        ]);

        // 購入済みにする
        SoldItem::create([
            'item_id' => $item1->id,
            'user_id' => $user->id,
            'sending_postcode' => '123-4567',
            'sending_address' => '東京都渋谷区',
            'sending_building' => 'テストビル',
        ]);

        // ログインユーザーとしてアクセス
        $this->actingAs($user);
        $response = $this->get(route('items.index', ['tab' => 'mylist']));

        // 1. いいねした商品だけ表示
        $response->assertSee($item1->name);
        $response->assertDontSee($item2->name);

        // 2. 購入済み商品に "Sold" ラベル
        $response->assertSee('Sold');

        // 未認証の場合は何も表示されない
        auth()->logout();
        $responseGuest = $this->get(route('items.index', ['tab' => 'mylist']));
        $responseGuest->assertDontSee($item1->name);
        $responseGuest->assertDontSee('Sold');
    }

}
