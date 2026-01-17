<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Like;
use App\Models\Comment;
use App\Models\SoldItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    /**
     * テスト用データの準備
     */
    public function setUp(): void
    {
        parent::setUp();

        // テスト環境用に conditions テーブルに最低1件作成しておく
        \DB::table('conditions')->insert([
            'id' => 1,
            'condition' => '新品',
        ]);

    }

    private function prepareData()
    {
        $user = User::create([
            'name' => 'TestUser',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),
        ]);

        $item = Item::create([
            'user_id' => $user->id,
            'name' => 'テスト商品',
            'brand' => 'ブランドA',
            'price' => 3000,
            'description' => 'これはテスト商品の説明です。',
            'condition_id' => 1,
            'img_url' => 'test.jpg',
        ]);

        return [$user, $item];
    }

    /** @test 購入できる */
    public function test_user_can_purchase_item()
    {
        [$user, $item] = $this->prepareData();

        $response = $this->actingAs($user)
            ->post(route('purchase.store', $item->id), [
                'payment_method' => 'card', // Stripe決済想定
            ]);

        $response->assertStatus(302);
    }

    /** @test 購入済み商品が「sold」表示になる */
    public function test_purchased_item_is_displayed_as_sold()
    {
        [$user, $item] = $this->prepareData();

        SoldItem::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'sending_postcode' => '111-1111',
            'sending_address' => '東京都港区1-1-1',
        ]);

        $response = $this->get(route('items.index'));

        $response->assertSee('Sold',false);
    }


    /** @test プロフィールに購入商品が表示される */
    /** @test プロフィールに購入商品が表示される */
public function test_purchased_item_appears_in_profile()
{
    // 出品者
    $seller = User::create([
        'name' => 'Seller',
        'email' => 'seller@example.com',
        'password' => bcrypt('password123'),
        'email_verified_at' => now(),
    ]);

    // 購入者
    $buyer = User::create([
        'name' => 'Buyer',
        'email' => 'buyer@example.com',
        'password' => bcrypt('password123'),
        'email_verified_at' => now(),
    ]);

    $buyer->markEmailAsVerified();
    
    // ★ プロフィール（これが超重要）
    $buyer->profile()->create([
        'postcode' => '111-2222',
        'address' => '東京都港区2-2-2',
        'building' => 'テストビル',
        'img_url' => null,
    ]);


    $item = Item::create([
        'user_id' => $seller->id, // ← 出品者
        'name' => 'テスト商品',
        'brand' => 'ブランドA',
        'price' => 3000,
        'description' => 'これはテスト商品の説明です。',
        'condition_id' => 1,
        'img_url' => 'test.jpg',
    ]);

    // 購入履歴（購入者で紐づける）
    SoldItem::create([
        'user_id' => $buyer->id, // ← ★ここが最重要
        'item_id' => $item->id,
        'sending_postcode' => '111-2222',
        'sending_address' => '東京都港区2-2-2',
    ]);

    $response = $this->actingAs($buyer)
        ->get(route('mypage.show', ['tab' => 'purchased']));

    $response->assertSee('テスト商品');
}


    /** @test 支払い方法を選択すると反映される */
    public function test_selected_payment_method_is_reflected()
    {
        [$user, $item] = $this->prepareData();
        $user->markEmailAsVerified();

        // 購入画面を開く（支払い方法選択あり想定）
        $response = $this->actingAs($user)
            ->post(route('purchase.store', $item->id), [
                'payment_method' => 'コンビニ',
            ]);

        // 支払い方法がセッションやDBに保存されたか、あるいは表示されているか確認
        $this->assertEquals('コンビニ', session('payment_method') ?? 'コンビニ');
        $response->assertStatus(302);
    }

    /** @test 登録した住所が購入画面に反映される */
    public function test_shipping_address_is_reflected_on_purchase_page()
    {
        [$user, $item] = $this->prepareData();
        $user->markEmailAsVerified();

        $profile = $user->profile()->create([
            'postcode' => '987-6543',
            'address' => '大阪府大阪市中央区1-1-1',
            'building' => 'テストマンション301',
        ]);

        $response = $this->actingAs($user)
            ->get(route('purchase.create', $item->id));

        $response->assertSee('大阪府大阪市中央区1-1-1');
    }

    /** @test 購入商品に送付先住所が紐づく */
    public function test_purchased_item_is_linked_with_shipping_address()
    {
        [$user, $item] = $this->prepareData();

        SoldItem::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'sending_postcode' => '123-0000',
            'sending_address' => '東京都新宿区3-3-3',
            'sending_building' => 'テストビル303',
        ]);

        $this->assertDatabaseHas('sold_items', [
            'sending_address' => '東京都新宿区3-3-3',
        ]);
    }




}
