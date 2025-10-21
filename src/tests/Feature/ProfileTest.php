<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;
use App\Models\Item;
use App\Models\SoldItem;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        \DB::table('conditions')->insert([
            'id' => 1,
            'condition' => '新品',
        ]);

    }

    /**
     * テスト用データ作成ヘルパー
     */
    private function prepareUserAndProfile()
    {
        $user = User::create([
            'name' => 'TestUser',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),
        ]);

        $profile = $user->profile()->create([
            'postcode' => '123-4567',
            'address' => '東京都渋谷区1-2-3',
            'building' => '渋谷ハイツ202',
            'img_url' => 'test_profile.jpg',
        ]);

        return [$user, $profile];
    }

    /**
     * プロフィール画像・ユーザー名・出品商品・購入商品が表示される
     */
    public function test_profile_page_displays_user_info_and_items()
    {
        [$user, $profile] = $this->prepareUserAndProfile();

        $user = User::find($user->id);

        $anotherUser = User::create([
            'name' => 'SellerUser',
            'email' => 'seller@example.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),
        ]);

        // 出品商品
        $item1 = Item::create([
            'user_id' => $user->id,
            'name' => 'テスト商品A',
            'brand' => 'ブランドA',
            'price' => 2000,
            'description' => '説明A',
            'condition_id' => 1,
            'img_url' => 'itemA.jpg',
        ]);

        // 購入商品
        $item2 = Item::create([
            'user_id' => $anotherUser->id,
            'name' => 'テスト商品B',
            'brand' => 'ブランドB',
            'price' => 3000,
            'description' => '説明B',
            'condition_id' => 1,
            'img_url' => 'itemB.jpg',
        ]);

        SoldItem::create([
            'user_id' => $user->id,
            'item_id' => $item2->id,
            'sending_postcode' => '999-9999',
            'sending_address' => '東京都港区3-3-3',
        ]);

        // ▼ 出品商品（selling タブ）
        $response = $this
            ->actingAs($user)
            ->withoutMiddleware(\Illuminate\Auth\Middleware\EnsureEmailIsVerified::class)
            ->get(route('mypage.show', ['tab' => 'selling']));


        $response->assertStatus(200);
        $response->assertSee('TestUser');
        $response->assertSee('テスト商品A'); // 出品商品
        $response->assertSee('test_profile.jpg'); // プロフィール画像
        $response->assertDontSee('テスト商品B');

        // ▼ 購入商品（purchased タブ）
        $response = $this
        ->actingAs($user)
        ->withoutMiddleware(\Illuminate\Auth\Middleware\EnsureEmailIsVerified::class)
        ->get(route('mypage.show', ['tab' => 'purchased']));

        $response->assertStatus(200);
        $response->assertSee('テスト商品B');
        $response->assertDontSee('テスト商品A'); // 出品品はここでは出ない
    }

    /**
     * 14. ユーザー情報変更
     * 編集画面で過去設定された初期値が表示される
     */
    public function test_profile_edit_page_displays_initial_values()
    {
        [$user, $profile] = $this->prepareUserAndProfile();

        $user = User::find($user->id);

        $response = $this
        ->withoutMiddleware(\Illuminate\Auth\Middleware\EnsureEmailIsVerified::class)
        ->actingAs($user)
        ->get(route('mypage.edit'));


        $response->assertStatus(200);
        $response->assertSee('TestUser'); // 名前
        $response->assertSee('123-4567'); // 郵便番号
        $response->assertSee('東京都渋谷区1-2-3'); // 住所
        $response->assertSee('渋谷ハイツ202'); // 建物
    }


        /**
     * 15. 出品商品情報登録
     * 商品出品画面にて必要な情報が保存できること
     */
    public function test_item_can_be_created_and_saved_correctly()
    {
        Storage::fake('public');

        [$user, $profile] = $this->prepareUserAndProfile();

        \DB::table('categories')->insert([
            'id' => 1,
            'category' => 'テストカテゴリ',
        ]);
        

        // ログイン状態で商品出品リクエストを送信
        $response = $this
            ->actingAs($user)
            ->withoutMiddleware(\Illuminate\Auth\Middleware\EnsureEmailIsVerified::class)
            ->post(route('items.store'), [
                'name' => 'テスト商品C',
                'brand' => 'ブランドC',
                'price' => 5000,
                'description' => '説明C',
                'condition_id' => 1,
                'categories' => [1], // カテゴリが必須ならここも
                'img_url' => UploadedFile::fake()->create('test_item.jpg', 100, 'image/jpeg'),
            ]);

        // 保存後、リダイレクト確認（通常はトップやマイページなど）
        $response->assertStatus(302);

        // DBに正しく保存されているか確認
        $this->assertDatabaseHas('items', [
            'name' => 'テスト商品C',
            'brand' => 'ブランドC',
            'price' => 5000,
            'description' => '説明C',
            'condition_id' => 1,
            'user_id' => $user->id,
        ]);
    }

}
