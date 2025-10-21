@extends('layouts.top')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
<div class="profile-form">
    <div class="profile-form__content">
        <div class="content__title">
            <h2>プロフィール設定</h2>
        </div>

        <form class="form" action="{{ isset($profile) ? route('mypage.update') : route('profile.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if(isset($profile))
            @method('PUT')
        @endif

            {{-- プロフィール画像 --}}
            <div class="form__image">
                <div class="form-group__image">
                    @if(!empty($profile->img_url))
                        <img class="profile__image" src="{{ asset('storage/' . $profile->img_url) }}" alt="プロフィール画像" >
                    @else
                        <div class="profile__image profile__image--default"></div>
                    @endif
                </div>
                <div class="form-group__button">
                    <label for="img_url" class="image__label">画像を選択する</label>
                    <input type="file" name="img_url" id="img_url" class="hidden">
                </div>
                    @error('img_url')
                        <div class="text__error">{{ $message }}</div>
                    @enderror
            </div>

            {{-- ユーザー名 --}}
            <div class="form-group">
                <div class="group__title">
                    <label for="name" class="group__name">ユーザー名</label>
                </div>
                <div class="group__text">
                    <input type="text" name="name" id="name" value="{{ old('name', auth()->user()->name) }}">
                </div>
                @error('name')
                <div class="text__error">{{ $message }}</div>
                @enderror
            </div>

            {{-- 郵便番号 --}}
            <div class="form-group">
                <div class="group__title">
                    <label for="postcode" class="group__name">郵便番号</label>
                </div>
                <div class="group__text">
                    <input type="text" name="postcode" id="postcode" value="{{ old('postcode', $profile->postcode ?? '') }}">
                </div>
                @error('postcode')
                <div class="text__error">{{ $message }}</div>
                @enderror
            </div>

            {{-- 住所 --}}
            <div class="form-group">
                <div class="group__title">
                    <label for="address" class="group__name">住所</label>
                </div>
                <div class="group__text">
                    <input type="text" name="address" id="address" value="{{ old('address', $profile->address ?? '') }}">
                </div>
                @error('address')
                <div class="text__error">{{ $message }}</div>
                @enderror
            </div>

            {{-- 建物名 --}}
            <div class="form-group">
                <div class="group__title">
                    <label for="building" class="group__name">建物名</label>
                </div>
                <div class="group__text">
                    <input type="text" name="building" id="building" value="{{ old('building', $profile->building ?? '') }}">
                </div>
                @error('building')
                    <div class="text__error">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="profile__button">
                {{ isset($profile) ? '更新する' : '登録する' }}
            </button>
        </form>
    </div>
</div>
@endsection
