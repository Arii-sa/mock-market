<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest;


class AuthenticatedSessionController extends Controller
{
    //

    public function create()
    {
        return view('auth.login'); // login.blade.php を表示
    }


public function store(LoginRequest $request)
{
    // バリデーション（LoginRequest 内の rules(), messages() を使う）
    $request->validated();

    // LoginRequest の authenticate() を呼び出す
    $request->authenticate();

    // ログイン成功後、セッション再生成
    $request->session()->regenerate();


    $user = Auth::user();

        // メール認証していない場合
        if (!$user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        // プロフィール未登録ならプロフィール登録画面へ
        if (!$user->profile) {
            return redirect()->route('profile.create');
        }

        // それ以外はマイリストへ
        return redirect()->route('items.index', ['tab' => 'mylist']);
    
}


    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
