@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
<div class="login-form__content">
    <div class="login-form__heading">
        <h2>ログイン</h2>
    </div>
    <form class="form" method="POST" action="{{ route('login') }}">
        @csrf
        <div class="form-group">
            <div class="form-group__title">
                <label class="form-group__label">メールアドレス</label>
            </div>
            <div class="form-group__content">
                <div class="form-group__text">
                    <input class="form-group__input" type="text" name="email" value="{{ old('email') }}">
                </div>
                <div class="form__error">
                    @error('email') <p>{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="form-group__title">
                <label lass="form-group__label">パスワード</label>
            </div>
            <div class="form-group__content">
                <div class="form-group__text">
                    <input class="form-group__input" type="password" name="password">
                </div>
                <div class="form__error">
                    @error('password') <p>{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="form__button">
            <button class="login-button" type="submit">ログインする</button>
        </div>
    </form>

    <div class="register__link">
        <a class="register-button" href="{{ route('register') }}">会員登録はこちら</a>
    </div>
</div>

@endsection
