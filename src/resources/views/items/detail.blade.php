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
                            <form action="{{ route('items.unlike', $item->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="unlike__button liked">☆</button>
                            </form>
                        @else
                            <form action="{{ route('items.like', $item->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="like__button">☆</button>
                            </form>
                        @endif
                    @else
                        <span class="like__icon">☆</span>
                    @endauth
                        <span class="like__count">{{ $item->likes->count() }}</span>
                    </div>

                    {{-- コメント --}}
                    <div class="comment-box">
                        <span class="comment__icon">💬</span>
                        <span class="comment__count">{{ $item->comments->count() }}</span>
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
            <div class="item-content">
                <h3 class="comment__tittle">コメント({{ $item->comments->count() }})</h3>
            </div>
            <div class="item-content">
                @forelse($item->comments as $comment)
                    <p class="comment__user">{{ $comment->user->name }}</p>
                    <p class="comment__show">{{ $comment->comment }}</p>
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
