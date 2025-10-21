<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Actions\Fortify\CreateNewUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use App\Http\Requests\RegisterRequest;



class RegisteredUserController extends Controller
{
    // 登録画面表示
    public function create()
    {
        return view('auth.register'); // resources/views/auth/register.blade.php
    }

    // 登録処理
    public function store(RegisterRequest $request)
    {

        // ユーザー作成
        $user = (new CreateNewUser())->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        // 登録イベントを発火（メール送信用）
        event(new Registered($user));

        // 登録後はログイン
        Auth::login($user);

        // メール認証画面へリダイレクト
        return redirect()->route('verification.notice');
    }


}
