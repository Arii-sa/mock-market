@extends('layouts.transaction')

@section('title', '取引チャット（出品者）')

@section('css')
<link rel="stylesheet" href="{{ asset('css/transactions/seller.css') }}">
<link rel="stylesheet" href="{{ asset('css/transactions/evaluation_modal.css') }}">
@endsection

@section('content')
<div class="transaction-container">

    <div class="seller-transaction-header">
        <div class="partner">
            <div class="partner-user">
                @php
                        $buyerProfile = $transaction->buyer->profile;
                        $buyerImg = $buyerProfile && $buyerProfile->img_url
                            ? asset('storage/' . $buyerProfile->img_url)
                            : null;
                    @endphp

                {{-- プロフィール画像 --}}
                @if($buyerImg)
                    <img class="partner-image" src="{{ $buyerImg }}" alt="" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                @endif
                    <div class="partner-image partner-image--default" style="{{ $buyerImg ? 'display:none;' : '' }}"></div>

                <p class="partner-name">
                    「 {{ $transaction->buyer->name }} 」さんとの取引画面
                </p>
            </div>
        </div>
    </div>

    {{-- 商品情報 --}}
    <div class="transaction-item">
        <div class="image-detail">
            <img class="item-image" src="{{ asset('storage/' . $transaction->item->img_url) }}" alt="">
        </div>
        <div class="item-detail">
            <p class="item-name">{{ $transaction->item->name }}</p>
            <p class="item-price">¥{{ number_format($transaction->item->price) }}</p>
        </div>
    </div>

    {{-- チャット --}}
    <div class="chat-area">
        <div class="message-area">
            @foreach ($messages as $message)
            @php
                $isMe = $message->user_id === auth()->id();
                $profile = $message->user->profile;
                $imgPath = $profile && $profile->img_url
                    ? asset('storage/' . $profile->img_url)
                    : null;
            @endphp

            <div class="chat {{ $isMe ? 'me' : 'other' }}">
                <div class="chat-user {{ $isMe ? 'right' : 'left' }}">
                    <div class="name-body">
                        {{-- 相手の画像（左） --}}
                        @if(!$isMe)
                            @if($imgPath)
                                <img class="chat-user-image" src="{{ $imgPath }}" alt="" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                            @endif
                                <div class="chat-user-image chat-user-image--default" style="{{ $imgPath ? 'display:none;' : '' }}"></div>
                        @endif

                        {{-- 名前 + メッセージ --}}
                            <p class="message-sender">{{ $message->user->name }}</p>

                        {{-- 自分の画像（右） --}}
                        @if($isMe)
                            @if($imgPath)
                                <img class="chat-user-image" src="{{ $imgPath }}" alt="" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                            @endif
                                <div class="chat-user-image chat-user-image--default" style="{{ $imgPath ? 'display:none;' : '' }}"></div>

                        @endif
                    </div>

                    <div class="message-body">
                        {{-- 削除済み --}}
                        @if ($message->deleted_at)
                            <p class="message-content deleted">
                                このメッセージは削除されました
                            </p>
                        @else
                            <p class="message-content">
                                {{ $message->body }}

                                @if ($message->edited_at)
                                    <span class="edited-label">(編集済)</span>
                                @endif
                            </p>

                            @if ($message->image_path)
                                <div class="message-image">
                                    <img src="{{ asset('storage/' . $message->image_path) }}" alt="送信画像">
                                </div>
                            @endif

                            {{-- 自分のメッセージだけ --}}
                            @if ($message->user_id === auth()->id())
                                <div class="message-actions">
                                    <a href="{{ route('transactions.show', [$transaction, 'edit_message_id' => $message->id]) }}" class="message-edit-btn">
                                        編集
                                    </a>
                                    <a href="{{ route('transactions.show', [$transaction, 'delete_message_id' => $message->id]) }}" class="message-detail-btn">
                                        削除
                                    </a>
                                </div>
                            @endif
                        @endif
                    </div>

                </div>
            </div>
            @endforeach

            @php
                $deletingMessage = $messages->firstWhere('id', request('delete_message_id'));
            @endphp

            @if ($deletingMessage && $deletingMessage->user_id === auth()->id())
                <div class="delete-confirm">
                    <p>このメッセージを削除しますか？</p>

                    <form method="POST"
                        action="{{ route('transactions.messages.destroy', [$transaction, $deletingMessage]) }}">
                        @csrf
                        @method('DELETE')

                        <button class="delate-btn" type="submit">削除する</button>
                        <a href="{{ route('transactions.show', $transaction) }}" class="delate-cancel-btn">キャンセル</a>
                    </form>
                </div>
            @endif
        </div>

        {{-- メッセージ送信 --}}
        @php
            $editingMessage = $messages->firstWhere('id', request('edit_message_id'));
        @endphp

        <div class="send-message">
            @if ($transaction->status === 'trading')
                {{-- 編集モード --}}
                @if ($editingMessage && $editingMessage->user_id === auth()->id())
                    <form method="POST"
                        action="{{ route('transactions.messages.update', [$transaction, $editingMessage]) }}">
                        @csrf
                        @method('PUT')

                        <p class="editing-label">メッセージを編集中</p>

                        <div class="edit-body">
                            <input class="message-text"
                                type="text"
                                name="body"
                                value="{{ old('body', $editingMessage->body) }}">
                        </div>

                        <div class="edit-actions">
                            <button class="edit-btn" type="submit">更新</button>
                            <a href="{{ route('transactions.show', $transaction) }}" class="edit-cancel-btn">キャンセル</a>
                        </div>
                    </form>

                {{-- 通常送信モード --}}
                @else
                    <form class="chat-form"
                        method="POST"
                        action="{{ route('transactions.messages.store', $transaction) }}"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="text-body">
                            <div class="error-message">
                                @error('body')
                                    <div class="text__error">{{ $message }}</div>
                                @enderror
                            </div>
                            <input class="message-text"
                                type="text" name="body"
                                placeholder="取引メッセージを記入してください"
                                value="{{ old('body') }}">
                        </div>

                        <div class="image-body">
                            <div class="error-message">
                                @error('image')
                                    <div class="text__error">{{ $message }}</div>
                                @enderror
                            </div>
                            <label for="send-image" class="image-upload-btn">
                                画像を追加
                            </label>
                            <input
                                id="send-image"
                                class="send-image"
                                type="file"
                                name="image"
                                accept="image/*"
                            >
                        </div>

                        <button class="btn-mark" type="submit">
                            <img src="{{ asset('images/send.jpg') }}" alt="ロゴ" class="btn-mark-img">
                        </button>
                    </form>
                @endif
            @endif
        </div>

        {{-- 評価モーダル（購入者） --}}
        @if ($showEvaluationModal)
            @include('transactions.evaluation_modal')
        @endif
    </div>
</div>
@endsection