<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Http\Requests\AddressRequest;

class AddressController extends Controller
{
    // 住所編集画面
    public function edit(Item $item)
    {
        // セッションに保存済みの住所があれば表示
        $address = session("purchase_address.{$item->id}", [
            'sending_postcode' => '',
            'sending_address' => '',
            'sending_building' => '',
        ]);

        return view('items.address', compact('item', 'address'));
    }

    // 入力された住所をセッションに保存
    public function update(AddressRequest $request, Item $item)
    {

        session([
            "purchase_address.{$item->id}" => $request->only([
                'sending_postcode',
                'sending_address',
                'sending_building',
            ])
        ]);

        return redirect()->route('purchase.create', $item->id)
            ->with('success', '住所を変更しました！');
    }
}
