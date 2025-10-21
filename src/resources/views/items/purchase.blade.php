@extends('layouts.top')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
<div class="purchase-content">
    <div class="content-all">
        {{-- 左カラム --}}
        <div class="content-left">
            {{-- 商品情報 --}}
            <div class="content-left__top">
                <div class="content-left__left">
                    <img class="content-left__img"
                        src="{{ asset('storage/' . $item->img_url) }}"
                        alt="{{ $item->name }}">
                </div>
                <div class="content-left__right">
                    <h2 class="item-name">{{ $item->name }}</h2>
                    <p class="item-price">¥{{ number_format($item->price) }}</p>
                </div>
            </div>

            {{-- 支払い方法 --}}
            <form action="{{ route('purchase.store', $item->id) }}" method="GET">
            @csrf
                <div class="payment-section">
                    <h3 class="payment-title">支払い方法</h3>
                    <div class="select-wrapper">
                        <select class="payment-select" name="payment_method" id="payment_method" onchange="this.form.submit()" required>
                            <option value="">選択してください</option>
                            <option value="convenience"
                                {{ old('payment_method', $selectedMethod) === 'convenience' ? 'selected' : '' }}>
                                コンビニ払い
                            </option>
                            <option value="card"
                                {{ old('payment_method', $selectedMethod) === 'card' ? 'selected' : '' }}>
                                カード払い
                            </option>
                        </select>
                    </div>
                    @error('payment_method')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>
            </form>

            {{-- 配送先 --}}
            <div class="address-section">
                <div class="address-header">
                    <h3 class="address-title">配送先</h3>
                    <a href="{{ route('address.edit', $item->id) }}" class="address-change">変更する</a>
                </div>
                <p>〒 {{ $address['sending_postcode'] ?? 'XXX-YYYY' }}</p>
                <p>{{ $address['sending_address'] ?? 'ここには住所が入ります' }}</p>
                @if(!empty($address['sending_building']))
                    <p>{{ $address['sending_building'] }}</p>
                @endif
            </div>
        </div>

        {{-- 右側：購入概要 --}}
        <div class="content-right">
            <div class="right-grid">
                <div class="summary-box">
                    <div class="summary-row">
                        <span>商品代金</span>
                        <span>¥{{ number_format($item->price) }}</span>
                    </div>
                    <div class="summary-row">
                        <span>支払い方法</span>
                        <span>
                        @if($selectedMethod === 'convenience')
                            コンビニ払い
                        @elseif($selectedMethod === 'card')
                            カード払い
                        @else
                            未選択
                        @endif
                        </span>
                    </div>
                </div>

                </form>
                {{-- 購入ボタンフォーム --}}
                <form action="{{ route('purchase.store', $item->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="payment_method" value="{{ $selectedMethod }}">
                    <button type="submit" class="purchase__button">購入する</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
