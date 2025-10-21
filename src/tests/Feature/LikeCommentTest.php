<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Like;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class LikeCommentTest extends TestCase
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


    /**
     * いいね登録テスト
     */
    public function test_user_can_like_an_item()
    {
        [$user, $item] = $this->prepareData();

        $this->actingAs($user)
            ->post(route('items.like', $item->id));

        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    /**
     * いいね解除テスト
     */
    public function test_user_can_unlike_an_item()
    {
        [$user, $item] = $this->prepareData();

        // 先にいいねを登録
        Like::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $this->actingAs($user)
            ->delete(route('items.unlike', $item->id));

        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    /**
     * コメント投稿テスト（ログイン済み）
     */
    public function test_logged_in_user_can_post_comment()
    {
        [$user, $item] = $this->prepareData();

        $response = $this->actingAs($user)
            ->post(route('comments.store', $item->id), [
                'comment' => 'とても良い商品です！',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'comment' => 'とても良い商品です！',
        ]);
    }

    /**
     * コメント投稿テスト（未ログイン時）
     */
    public function test_guest_cannot_post_comment()
    {
        [$user, $item] = $this->prepareData();

        $response = $this->post(route('comments.store', $item->id), [
            'comment' => 'ログインしていないコメント',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseMissing('comments', [
            'comment' => 'ログインしていないコメント',
        ]);
    }

    /**
     * コメントバリデーションテスト（空）
     */
    public function test_comment_cannot_be_empty()
    {
        [$user, $item] = $this->prepareData();

        $response = $this->actingAs($user)
            ->post(route('comments.store', $item->id), [
                'comment' => '',
            ]);

        $response->assertSessionHasErrors(['comment']);
    }

    /**
     * コメントバリデーションテスト（255文字以上）
     */
    public function test_comment_cannot_exceed_255_characters()
    {
        [$user, $item] = $this->prepareData();

        $longComment = str_repeat('あ', 256);

        $response = $this->actingAs($user)
            ->post(route('comments.store', $item->id), [
                'comment' => $longComment,
            ]);

        $response->assertSessionHasErrors(['comment']);
    }
}
