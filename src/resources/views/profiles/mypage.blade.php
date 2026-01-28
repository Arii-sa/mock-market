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
                <p class="name__title">{{ $user->name }} </p>
                @if ($user->average_rating)
                <div class="user-rating">
                    @for ($i = 1; $i <= 5; $i++)
                        <span class="star {{ $i <= floor($user->average_rating) ? 'filled' : '' }}">
                            ★
                        </span>
                    @endfor
                </div>
                @endif
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
                <a class="tab__name {{ $tab === 'selling' ? 'active' : '' }}"
                    href="{{ route('mypage.show', ['tab' => 'selling']) }}">
                    出品した商品
                </a>
            </div>
            <div class="content-tab">
                <a class="tab__name {{ $tab === 'purchased' ? 'active' : '' }}"
                    href="{{ route('mypage.show', ['tab' => 'purchased']) }}">
                    購入した商品
                </a>
            </div>
            <div class="content-tab">
                <a class="tab__name {{ $tab === 'trading' ? 'active' : '' }}"
                href="{{ route('mypage.show', ['tab' => 'trading']) }}">
                    取引中の商品

                    @if($unreadTotal > 0)
                        <span class="tab-badge">{{ $unreadTotal }}</span>
                    @endif
                </a>
            </div>
        </div>

        {{-- 商品グリッド --}}
        <div class="mypage-item">
            @if ($tab === 'trading')
                @forelse ($items as $transaction)
                    <div class="item-list">
                        <a href="{{ route('transactions.show', $transaction->id) }}">
                            <div class="item-image-wrapper">
                                <img class="item__image"
                                        src="{{ asset('storage/' . $transaction->item->img_url) }}"
                                        alt="{{ $transaction->item->name }}">

                                @if ($transaction->unread_count > 0)
                                    <span class="unread-badge">
                                        {{ $transaction->unread_count }}
                                    </span>
                                @endif

                            </div>
                        </a>
                        <p class="item__name">{{ $transaction->item->name }}</p>
                    </div>
                @empty
                    <p class="item__message">取引中の商品はありません。</p>
                @endforelse
            @else
                @forelse ($items as $item)
                    <div class="item-list">
                        <a href="{{ route('items.show', $item->id) }}">
                            <img class="item__image"
                                src="{{ asset('storage/' . $item->img_url) }}"
                                alt="{{ $item->name }}">
                        </a>

                        {{-- ★購入済みならSold表示★ --}}
                        @if ($item->isSold())
                            <span class="item__sold">Sold</span>
                        @elseif ($item->isTrading())
                            <span class="item__trading">取引中</span>
                        @endif


                        <p class="item__name">{{ $item->name }}</p>
                    </div>
                @empty
                    <p class="item__message">商品が見つかりません。</p>
                @endforelse
            @endif

        </div>
    </div>
</div>
@endsection
