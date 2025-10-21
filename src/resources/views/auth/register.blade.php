@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('content')
<div class="register-form__content">
    <div class="register-form__heading">
        <h2>会員登録</h2>
    </div>
    <form class="form" method="POST" action="{{ route('register') }}">
    @csrf
        <div class="form-group">
            <div class="form-group__title">
                <label class="form-group__label">ユーザー名</label>
            </div>
            <div class="form-group__content">
                <div class="form-group__text">
                    <input class="form-group__input" type="text" name="name" value="{{ old('name') }}">
                </div>
                <div class="form__error">
                    @error('name') <p>{{ $message }}</p> @enderror
                </div>
            </div>
        </div>
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
        <div>
        <div class="form-group">
            <div class="form-group__title">
                <label class="form-group__label">パスワード</label>
            </div>
            <div class="form-group__content">
                <div class="form-group__text">
                    <input class="form-group__input" type="password" name="password">
                </div>
                <div class="form__error">
                    @error('password') <p>{{ $message }}</p> @enderror
                </div>
            </div>
        <div>
        <div class="form-group">
            <div class="form-group__title">
                <label class="form-group__label">確認用パスワード</label>
            </div>
            <div class="form-group__content">
                <div class="form-group__text">
                    <input class="form-group__input" type="password" name="password_confirmation">
                </div>
                <div class="form__error">
                    @error('password_confirmation') <p>{{ $message }}</p> @enderror
                </div>
            </div>
        </div>
        <div class="form__button">
            <button class="register-button" type="submit">登録する</button>
        </div>
    </form>
    <div class="login__link">
        <a class="login__button" href="{{ route('login') }}">ログインはこちら</a>
    </div>
</div>
@endsection
