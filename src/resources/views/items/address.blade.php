@extends('layouts.top')

@section('css')
<link rel="stylesheet" href="{{ asset('css/address.css') }}">
@endsection

@section('content')
<div class="address-form">
    <div class="address-form__heading">
        <h2 class="address-form__title">住所の変更</h2>
    </div>
    <form class="form" action="{{ route('address.update', $item->id) }}" method="POST">
        @csrf
        {{-- 郵便番号 --}}
        <div class="form-group">
            <div class="form-group__title">
                <label class="form-group__label">郵便番号</label>
            </div>
            <div class="form-group__content">
                <div class="form-group__text">
                    <input class="form-group__input"
                        type="text"
                        name="sending_postcode"
                        value="{{ old('sending_postcode', $address['sending_postcode']) }}">
                </div>
                <div class="form__error">
                    @error('sending_postcode')
                        <p>{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- 住所 --}}
        <div class="form-group">
            <div class="form-group__title">
                <label class="form-group__label">住所</label>
            </div>
            <div class="form-group__content">
                <div class="form-group__text">
                    <input class="form-group__input"
                    type="text"
                    name="sending_address"
                    value="{{ old('sending_address', $address['sending_address']) }}" >
                </div>
                <div class="form__error">
                @error('sending_address')
                    <p>{{ $message }}</p>
                @enderror
                </div>
            </div>
        </div>

        {{-- 建物名 --}}
        <div class="form-group">
            <div class="form-group__title">
                <label class="form-group__label">建物名</label>
            </div>
            <div class="form-group__content">
                <div class="form-group__text">
                    <input class="form-group__input"
                        type="text"
                        name="sending_building"
                        value="{{ old('sending_building', $address['sending_building']) }}">
                </div>
                <div class="form__error">
                @error('sending_building')
                    <p>{{ $message }}</p>
                @enderror
                </div>
            </div>
        </div>

        <div class="form__button">
            <button class="edit-button" type="submit">更新する</button>
        </div>
    </form>
</div>
@endsection


