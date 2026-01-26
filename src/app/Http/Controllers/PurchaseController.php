<?php

namespace App\Http\Controllers;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\PaymentIntent;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\SoldItem;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PurchaseRequest;
use App\Enums\ItemStatus;


class PurchaseController extends Controller
{
    
    public function create(Item $item, Request $request)
    {
        if ($item->isSold()) {
            return redirect()->route('items.index')->with('error', 'この商品はすでに購入されています。');
        }

        // セッションに保存した住所があれば優先
        $address = session("purchase_address.{$item->id}", [
            'sending_postcode' => auth()->user()->profile->postcode ?? '',
            'sending_address' => auth()->user()->profile->address ?? '',
            'sending_building' => auth()->user()->profile->building ?? '',
        ]);

        $selectedMethod = $request->query('payment_method', '');

        $isConfirm = false;

        return view('items.purchase', compact('item', 'address', 'selectedMethod', 'isConfirm'));
    }

    public function store(PurchaseRequest $request, Item $item)
    {
        if ($item->isSold()) {
            return redirect()->route('items.index')->with('error', 'この商品はすでに購入されています。');
        }


        $selectedMethod = $request->input('payment_method');

        // セッションから住所を取得
        $address = session("purchase_address.{$item->id}", [
            'sending_postcode' => auth()->user()->profile->postcode ?? '',
            'sending_address' => auth()->user()->profile->address ?? '',
            'sending_building' => auth()->user()->profile->building ?? '',
        ]);

        if (!$address) {
            return redirect()->route('address.edit', $item->id)
                ->with('error', '住所を入力してください。');
        }

        // --- Stripe 決済処理 ---
        if (in_array($selectedMethod, ['card', 'convenience'])) {

            // Stripe APIキー設定
            Stripe::setApiKey(config('services.stripe.secret'));

            // 支払い方法ごとのモード
            $paymentMethodTypes = $selectedMethod === 'card'
                ? ['card']
                : ['konbini']; // Stripeの「コンビニ払い」

            // Stripe Checkout Session作成
            $session = Session::create([
                'payment_method_types' => $paymentMethodTypes,
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'jpy',
                        'product_data' => [
                            'name' => $item->name,
                        ],
                        'unit_amount' => $item->price, // 円単位（整数）
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('purchase.success', ['item' => $item->id]),
                'cancel_url'  => route('purchase.cancel', ['item' => $item->id]),
                'metadata' => [
                    'user_id' => Auth::id(),
                    'item_id' => $item->id,
                ],
            ]);

            // Stripe決済画面にリダイレクト
            return redirect($session->url);
        }

        // --- Stripe以外の支払い（今後追加用） ---
        return back()->with('error', '対応していない支払い方法です。');

    }

    public function success(Item $item)
    {
        // --- 売り切れチェック ---
        if ($item->isSold()) {
            return redirect()->route('items.index')->with('error', 'この商品はすでに購入されています。');
        }

        Transaction::firstOrCreate(
            ['item_id' => $item->id],
            [
                'buyer_id'  => auth()->id(),
                'seller_id' => $item->user_id,
                'status'    => 'trading',
            ]
        );

        $item->update([
            'status' => ItemStatus::TRADING,
        ]);

        // --- セッション削除 ---
        session()->forget("purchase_address.{$item->id}");

        return redirect()->route('mypage.show', ['tab' => 'trading'])
            ->with('success', '購入が完了しました！取引を開始できます');
    }

    public function cancel(Item $item)
    {
        return redirect()
            ->route('purchase.create', $item->id)
            ->with('error', '決済がキャンセルされました。');
    }
}
