@extends('layouts.top')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="product-content">
    <div class="product-main">
        <div class="product__tab">
            {{-- タブ --}}
            <div class="tab-left">
                <a class="tab__name {{ $tab === 'items' ? 'active' : '' }}" href="{{ route('items.index', array_merge(request()->all(), ['tab' => 'items'])) }}">
                    おすすめ
                </a>
            </div>
            <div class="tab-right">
                <a class="tab__name {{ $tab === 'mylist' ? 'active' : '' }}" href="{{ route('items.index', array_merge(request()->all(), ['tab' => 'mylist'])) }}">
                    マイリスト
                </a>
            </div>
        </div>
        <div class="product__item">

            {{-- 未ログインでマイリストタブならメッセージ --}}
            @if($tab === 'mylist' && !auth()->check())
                <p>マイリストを表示するにはログインしてください。</p>
            @endif

            {{-- 商品グリッド --}}
            <div class="item__cards">
                @foreach ($items as $item)
                <div class="item-card">
                    <a class="card__link" href="{{ route('items.show', $item->id) }}">
                        <img class="card__image"
                            src="{{ asset('storage/' . $item->img_url) }}"
                            alt="{{ $item->name }}">
                    </a>
                    @if(method_exists($item, 'isSold') ? $item->isSold() : (isset($item->sold_item)))
                    <span class="card__sold">Sold</span>
                    @endif
                    <div class="card__detail">
                        <p class="card__name">{{ $item->name }}</p>
                    </div>
                </div>
                @endforeach

            </div>
        </div>
    </div>
</div>
@endsection
