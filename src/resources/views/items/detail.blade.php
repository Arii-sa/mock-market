@extends('layouts.top')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')
<div class="detail-content">
    <div class="detail-all">
        <div class="detail-left">
            {{-- 商品画像 --}}
            <img class="item__image"
            src="{{ asset('storage/' . $item->img_url) }}"
            alt="{{ $item->name }}">
        </div>
        <div class="detail-right">
            {{-- 商品名 --}}
            <div class="item-content">
                <h2 class="item__name">{{ $item->name }}</h2>
            </div>

            {{-- ブランド名 --}}
            <div class="item-content">
                @if($item->brand)
                    <p class="item__brand">{{ $item->brand }}</p>
                @endif
            </div>

            {{-- 価格 --}}
            <div class="item-content">
                <p class="item__price">¥{{ number_format($item->price) }} (税込)</p>
            </div>

            {{-- いいね・コメント --}}
            <div class="item-content">
                <div class="item__likes">
                    {{-- いいね --}}
                    <div class="like-box">
                        @auth
                            @if($item->likes->where('user_id', auth()->id())->count())
                                {{-- いいね解除 --}}
                                <form action="{{ route('items.unlike', $item->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="like-button liked">
                                        <i class="far fa-heart fa-2xl"></i>
                                    </button>
                                </form>
                            @else
                                {{-- いいね --}}
                                <form action="{{ route('items.like', $item->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="like-button">
                                        <i class="far fa-heart fa-2xl"></i>
                                    </button>
                                </form>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="like-button">
                                <i class="far fa-heart fa-2xl"></i>
                            </a>
                        @endauth


                        <span class="like-count">{{ $item->likes->count() }}</span>
                    </div>


                    {{-- コメント --}}
                    <div class="comment-box">
                        <span class="comment-button">
                            <i class="far fa-comment fa-2xl"></i>
                        </span>
                        <span class="comment-count">{{ $item->comments->count() }}</span>
                    </div>
                </div>
            </div>

            {{-- 購入ボタン（例: 購入ページへ遷移） --}}
            <div class="item-content">
                <a href="{{ route('purchase.create', $item->id) }}" class="item__purchase">
                    購入手続きへ
                </a>
            </div>

            {{-- 商品説明 --}}
            <div class="item-content">
                <h3 class="description__name">商品説明</h3>
                <p class="item__description">{{ $item->description }}</p>
            </div>

            {{-- 商品の情報 --}}
            <div class="item-content">
                <h3 class="information__name">商品の情報</h3>
            </div>
            <div class="item-content">
                <div class="categories-detail">
                    <p class="item__category">カテゴリー</p>
                    @foreach($item->categories as $category)
                    <p class="category__name">{{ $category->category }}</p>
                    @endforeach
                </div>
            </div>
            <div class="item-content">
                <div class="conditions-detail">
                    <p class="item__condition">商品の状態</p>
                    <p class="item-conditions">{{ $item->condition->condition }}</p>
                </div>
            </div>

            {{-- コメント一覧 --}}
            <div class="comments">
                {{-- タイトル --}}
                <div class="comment__tittle">
                    コメント({{ $item->comments->count() }})
                </div>

                @forelse($item->comments as $comment)
                    <div class="comment-item">
                        {{-- プロフィール画像＋名前 --}}
                        <div class="comment-header">
                            <div class="comment-user__image">
                                @php
                                    $imgPath = optional($comment->user->profile)->img_url
                                        ? asset('storage/' . $comment->user->profile->img_url)
                                        : null;
                                @endphp

                                @if($imgPath)
                                    <img src="{{ $imgPath }}" alt="プロフィール画像">
                                @else
                                    <div class="comment-user__image--default"></div>
                                @endif
                            </div>

                            <p class="comment__user">{{ $comment->user->name }}</p>
                        </div>

                        {{-- コメント --}}
                        <div class="comment-body">
                            <p class="comment__show">{{ $comment->comment }}</p>
                        </div>
                    </div>
                @empty
                    <p class="uncomment">コメントはまだありません。</p>
                @endforelse
            </div>

            {{-- コメント投稿フォーム --}}
            <div class="item-content">
                <h4 class="item__comment">商品へのコメント</h4>
            </div>
            <div class="item-content">
            @auth
                <form action="{{ route('comments.store', $item->id) }}" method="POST" class="comment__form">
                    @csrf
                    <textarea name="comment" rows="10" class="comment__text">{{ old('comment') }}</textarea>
                    <div class="error__message">
                        @error('comment')
                            <p>{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="comment__button">コメント送信</button>
                </form>
            @else
                <p>コメントするにはログインしてください。</p>
            @endauth
            </div>
        </div>
    </div>
</div>
@endsection
