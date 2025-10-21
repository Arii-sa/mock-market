@extends('layouts.top')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection

@section('content')
<div class="sell-content">
    <h2 class="title-top">商品の出品</h2>
    <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data" class="content-form">
        @csrf
        <div class="content-detail">
            {{-- 商品画像 --}}
            <div class="sell-detail">
                <label class="detail-title">商品画像</label>
                <div class="img-box">
                    <input type="file" name="img_url" id="img_url" hidden>
                    <label for="img_url" class="upload-btn">画像を選択する</label>

                    @error('img_url')
                        <p class="item-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="content-title">
                <h3 class="title-text">商品の詳細</h3>
            </div>

            {{-- カテゴリ（複数選択） --}}
            <div class="sell-detail">
                <label class="detail-title">カテゴリー</label>
                <div class="category-select">
                    @foreach($categories as $category)
                    <label class="cursor-pointer">
                        {{-- hidden の代わりに sr-only を使う --}}
                        <input type="checkbox" name="categories[]" value="{{ $category->id }}" class="category-item" {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}>
                        <span class="item-category"> {{ $category->category }} </span>
                    </label>
                    @endforeach
                </div>
                @error('categories')
                    <p class="item-error">{{ $message }}</p>
                @enderror
            </div>
            {{-- 商品の状態 --}}
            <div class="sell-detail">
                <label class="detail-title">商品の状態</label>
                <select name="condition_id" class="detail-text">
                    @foreach($conditions as $condition)
                    <option value="{{ $condition->id }}" {{ old('condition_id') == $condition->id ? 'selected' : '' }}> {{ $condition->condition }} </option>
                    @endforeach
                </select>
                @error('condition_id')
                    <p class="item-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="content-title">
                    <h3 class="title-text">商品名と説明</h3>
            </div>

            {{-- 商品名 --}}
            <div class="sell-detail">
                <label class="detail-title">商品名</label>
                <input type="text" name="name" value="{{ old('name') }}" class="detail-text">
                @error('name')
                    <p class="item-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- ブランド名 --}}
            <div class="sell-detail">
                <label class="detail-title">ブランド名</label>
                <input type="text" name="brand" value="{{ old('brand') }}" class="detail-text">
                @error('brand')
                    <p class="item-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- 商品説明 --}}
            <div class="sell-detail">
                <label class="detail-title">商品説明</label>
                <textarea name="description" rows="4" class="detail-text">{{ old('description') }}</textarea>
                @error('description')
                    <p class="item-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- 販売価格 --}}
            <div class="sell-detail">
                <label class="detail-title">販売価格</label>
                <input type="number" name="price" value="{{ old('price') }}" class="detail-text">
                @error('price')
                    <p class="item-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="content-button">
                <button type="submit" class="sell-button">出品する</button>
            </div>
        </div>
    </form>
</div>
@endsection