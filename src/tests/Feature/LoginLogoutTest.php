<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class LoginLogoutTest extends TestCase
{
    use RefreshDatabase;
    /**
     * @test メールアドレスが入力されていない場合
     */
    public function test_login_email_is_required()
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /**
     * @test パスワードが入力されていない場合
     */
    public function test_login_password_is_required()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    /**
     * @test 入力情報が間違っている場合
     */
    public function test_login_with_invalid_credentials()
    {
        // ダミーユーザー（登録済みとは違う情報でログインを試す）
        User::factory()->create([
            'email' => 'real@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'fake@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    /**
     * @test メール未認証ユーザーの場合、認証案内画面にリダイレクトされる
     */
    public function test_login_redirects_to_verification_if_email_unverified()
    {
        $user = User::factory()->create([
            'email' => 'unverified@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => null, // 未認証
        ]);

        $response = $this->post('/login', [
            'email' => 'unverified@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('verification.notice'));
    }

    /**
     * @test プロフィール未登録ユーザーはプロフィール登録画面へリダイレクトされる
     */
    public function test_login_redirects_to_profile_create_if_no_profile()
    {
        $user = User::factory()->create([
            'email' => 'noprofile@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        // プロフィールなし状態を想定

        $response = $this->post('/login', [
            'email' => 'noprofile@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('profile.create'));
    }

    /**
     * @test すべて正常な場合はマイリストにリダイレクトされる
     */
    public function test_login_success_redirects_to_mylist()
    {
        $user = User::factory()->create([
            'email' => 'ok@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        // プロフィールがあることをシミュレート
        $user->profile()->create([
            'name' => 'テストユーザー',
            'postcode' => '123-4567',
            'address' => '東京都渋谷区テスト1-1-1',
            'building' => 'テストビル101',
        ]);

        $response = $this->post('/login', [
            'email' => 'ok@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('items.index', ['tab' => 'mylist']));
        $this->assertAuthenticatedAs($user);
    }

    /**
     * @test ログアウトできる
     */
    public function test_logout_success()
    {
        $user = User::factory()->create([
            'email' => 'logout@example.com',
            'password' => Hash::make('password123'),
        ]);

        $this->actingAs($user);

        $response = $this->post('/logout');

        $response->assertRedirect(route('items.index'));
        $this->assertGuest();
    }

}
