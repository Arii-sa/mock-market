<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Like;
use App\Models\Comment;
use App\Models\Category;
use App\Models\Condition;
use App\Models\SoldItem;
use Illuminate\Support\Facades\DB;


class DetailTest extends TestCase
{
    use RefreshDatabase;

    /** @test 商品検索（部分一致） */
    public function test_search_items_by_name()
    {
        // 条件を作成
        $condition = Condition::create(['condition' => '新品']);

        // ユーザー作成
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

        // 商品作成
        $item1 = Item::create([
            'user_id' => $otherUser->id,
            'condition_id' => $condition->id,
            'name' => '赤いシャツ',
            'price' => 2000,
            'brand' => 'ブランドA',
            'description' => 'おしゃれな赤いシャツです',
            'img_url' => 'shirt.jpg',
        ]);

        $item2 = Item::create([
            'user_id' => $otherUser->id,
            'condition_id' => $condition->id,
            'name' => '青いズボン',
            'price' => 3000,
            'brand' => 'ブランドB',
            'description' => 'かっこいいズボン',
            'img_url' => 'pants.jpg',
        ]);

        // ログイン
        $this->actingAs($loginUser);

        // 部分一致検索（例：「シャツ」で検索）
        $response = $this->get(route('items.index', ['keyword' => 'シャツ']));

        // 検索結果：部分一致する商品が表示される
        $response->assertSee('赤いシャツ');
        $response->assertDontSee('青いズボン');

        // マイリストタブでも検索キーワードが保持されている
        $responseMylist = $this->get(route('items.index', ['tab' => 'mylist', 'keyword' => 'シャツ']));
        $responseMylist->assertSee('シャツ');
    }



    /** @test 商品詳細情報がすべて表示される */
    public function test_item_detail_shows_all_information()
    {
        // 各種データ作成
        $condition = Condition::create(['condition' => '中古']);
        $category1 = Category::create(['category' => 'ファッション']);
        $category2 = Category::create(['category' => 'トップス']);

        $user = User::create([
            'name' => 'User1',
            'email' => 'user1@example.com',
            'password' => bcrypt('password'),
        ]);

        $item = Item::create([
            'user_id' => $user->id,
            'condition_id' => $condition->id,
            'name' => 'テスト商品',
            'price' => 4000,
            'brand' => 'ブランドC',
            'description' => 'これはテスト用の商品説明です。',
            'img_url' => 'test.jpg',
        ]);

        // カテゴリを紐付け（多対多を想定）
        DB::table('category_items')->insert([
            ['item_id' => $item->id, 'category_id' => $category1->id],
            ['item_id' => $item->id, 'category_id' => $category2->id],
        ]);

        // いいね・コメントを作成
        Like::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $commentUser = User::create([
            'name' => 'Commenter',
            'email' => 'comment@example.com',
            'password' => bcrypt('password'),
        ]);

        Comment::create([
            'user_id' => $commentUser->id,
            'item_id' => $item->id,
            'comment' => 'とてもいい商品ですね！',
        ]);

        // 商品詳細ページへアクセス
        $response = $this->get(route('items.show', ['item' => $item->id]));

        // 商品の基本情報が表示されている
        $response->assertSee('テスト商品');
        $response->assertSee('ブランドC');
        $response->assertSee('4,000');
        $response->assertSee('これはテスト用の商品説明です。');
        $response->assertSee('test.jpg');

        // いいね数・コメント数
        $response->assertSee('1'); // like数・comment数を1件想定

        // コメント内容・ユーザー情報
        $response->assertSee('Commenter');
        $response->assertSee('とてもいい商品ですね！');

        // カテゴリ・商品の状態
        $response->assertSee('ファッション');
        $response->assertSee('トップス');
        $response->assertSee('中古');
    }
}
