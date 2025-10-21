@extends('layouts.top')

@section('content')
<div class="purchase-success">
    <h2>支払いが完了しました！</h2>
    <p>{{ $item->name }} の購入が完了しました。</p>
    <a href="{{ route('items.index') }}" class="btn">トップに戻る</a>
</div>
@endsection
