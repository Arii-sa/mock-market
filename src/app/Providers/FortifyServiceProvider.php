<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Profile;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Fortify\Contracts\LoginResponse;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        //
        $this->app->singleton(CreatesNewUsers::class, CreateNewUser::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        // 登録画面
        Fortify::registerView(function () {
            return view('auth.register'); // register.blade.php
        });

        // ログイン画面
        Fortify::loginView(function () {
            return view('auth.login'); // login.blade.php
        });

        Fortify::authenticateUsing(function (LoginRequest $request) {
            $request->validated();
            $request->authenticate(); // ← LoginRequest に書いた処理を実行
            return Auth::user();
        });

        $this->app->instance(LoginResponse::class, new class implements LoginResponse {
            public function toResponse($request)
            {
                $user = Auth::user();
    
                // メール未認証 → 認証案内へ
                if (! $user->hasVerifiedEmail()) {
                    return redirect()->route('verification.notice');
                }
    
                // プロフィール未作成 → プロフィール作成へ
                if (! $user->profile) {
                    return redirect()->route('profile.create');
                }
    
                // それ以外はマイリストへ
                return redirect()->route('mylist');
            }
        });
    }

}
