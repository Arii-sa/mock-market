@extends('layouts.top')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endsection

@section('content')
<div class="mypage-content">
    <div class="content-top">
        <div class="user-info">
            <div class="user-image">
                @php
                    $imgPath = $profile->img_url ? asset('storage/' . $profile->img_url) : null;
                @endphp

                @if($imgPath)
                    <img class="profile__image" src="{{ $imgPath }}" 
                        alt="プロフィール画像"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                @endif

                <div class="profile__image profile__image--default" 
                    style="{{ $imgPath ? 'display:none;' : '' }}"></div>
            </div>
            <div class="user-name">
                    <h3 class="name__title">{{ $user->name }} </h3>
            </div>
        </div>
        <div class="user-edit">
            <a href="{{ route('mypage.edit') }}"
            class="edit__button">
                プロフィールを編集
            </a>
        </div>
    </div>

    {{-- タブ --}}
    <div class="content-bottom">
        <div class="mypage-tab">
            <div class="content-tab">
                <a class="tab__name {{ $tab === 'selling' ? 'active' : '' }}" href="{{ route('mypage.show', ['tab' => 'selling']) }}">
                    出品した商品
                </a>
            </div>
            <div class="content-tab">
                <a class="tab__name {{ $tab === 'purchased' ? 'active' : '' }}" href="{{ route('mypage.show', ['tab' => 'purchased']) }}">
                    購入した商品
                </a>
            </div>
        </div>

        {{-- 商品グリッド --}}
        <div class="mypage-item">
            @forelse ($items as $item)
                <div class="item-list">
                    <a href="{{ route('items.show', $item->id) }}">
                        <img class="item__image"
                            src="{{ asset('storage/' . $item->img_url) }}"
                            alt="{{ $item->name }}">
                    </a>

                    {{-- ★購入済みならSold表示★ --}}
                    @if(method_exists($item, 'isSold') ? $item->isSold() : (isset($item->sold_item) && $item->sold_item))
                        <span class="item__sold">Sold</span>
                    @endif

                    <p class="item__name">{{ $item->name }}</p>
                </div>
            @empty
                <p class="item__message">商品が見つかりません。</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
