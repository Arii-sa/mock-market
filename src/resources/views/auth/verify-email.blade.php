@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/verify.css') }}">
@endsection

@section('content')
<div class="verify__container">
    <p class="verify__text">
        登録していただいたメールアドレスに確認メールを送付しました。<br>
        メール認証を完了してください。
    </p>

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="verify__button--resend">確認メールを再送する</button>
    </form>
</div>
@endsection
