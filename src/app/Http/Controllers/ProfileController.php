<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\SoldItem;
use App\Http\Requests\ProfileRequest;
use App\Models\Profile;
use App\Models\Transaction;
use App\Models\TransactionMessageRead;
use App\Enums\ItemStatus;

class ProfileController extends Controller
{
    // 初回プロフィール設定
    public function create()
    {
        $profile = auth()->user()->profile; // null OK

        return view('profiles.profile', compact('profile'));
    }


    public function store(ProfileRequest $request)
    {
        $validated = $request->validated();

        // usersテーブルのnameを更新
        $user = auth()->user();
        $user->name = $validated['name'];
        $user->save();

        if ($request->hasFile('img_url')) {
            $validated['img_url'] = $request->file('img_url')->store('profiles', 'public');
        }

        $validated['user_id'] = $user->id;

        Profile::create($validated);

        return redirect()->route('items.index', ['tab' => 'mylist'])
            ->with('success', 'プロフィールを登録しました！');

    }

    // マイページ表示
    public function show(Request $request)
    {
        $tab = $request->query('tab', 'selling'); // デフォルトは出品した商品
        $user = Auth::user();
        $profile = $user->profile;

        if ($tab === 'purchased') {
            // 購入した商品
            $items = $user->soldItems()
                ->with('item')
                ->get()
                ->map(function ($soldItem) {
                    $item = $soldItem->item;
                    $item->isSold = $item->status === ItemStatus::SOLD;
                    return $item;
                });
        }elseif ($tab === 'trading') {
            $items = Transaction::whereIn('status', ['trading', 'completed'])
                ->where(function ($query) use ($user) {
                    $query->where('buyer_id', $user->id)
                          ->orWhere('seller_id', $user->id);
                })
                ->with([
                    'item',
                    'evaluations',
                    'messages',
                    'messageReads' => function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    }
                ])
                ->orderByDesc('updated_at')
                ->get()
                ->map(function ($transaction) use ($user) {

                    $lastReadId = optional($transaction->messageReads->first())
                        ->last_read_message_id;

                    $transaction->unread_count = $transaction->messages
                        ->where('id', '>', $lastReadId ?? 0)
                        ->where('user_id', '!=', $user->id)
                        ->count();

                    return $transaction;
                });
        }else {
            // 出品した商品
            $items = $user->items()
                ->withCount(['likes', 'comments'])
                ->get();
        }

        // 取引中の未読メッセージ合計
        $unreadTotal = Transaction::whereIn('status', ['trading', 'completed'])
            ->where(function ($query) use ($user) {
                $query->where('buyer_id', $user->id)
                    ->orWhere('seller_id', $user->id);
            })
            ->with(['messages', 'messageReads' => function ($q) use ($user) {
                $q->where('user_id', $user->id);
            }])
            ->get()
            ->sum(function ($transaction) use ($user) {
                $lastReadId = optional($transaction->messageReads->first())
                    ->last_read_message_id;

                return $transaction->messages
                    ->where('id', '>', $lastReadId ?? 0)
                    ->where('user_id', '!=', $user->id)
                    ->count();
            });


        return view('profiles.mypage', compact('user', 'items', 'tab', 'profile', 'unreadTotal'));

    }


    // プロフィール編集画面
    public function edit()
    {
        $user = Auth::user();
        $profile = $user->profile;

        return view('profiles.profile', compact('profile'));
    }

    // プロフィール更新処理
    public function update(ProfileRequest $request)
    {
        $validated = $request->validated();

        $user = Auth::user();

        // ユーザー名は users テーブル
        $user->update([
            'name' => $validated['name'],
        ]);
    
        // プロフィール画像（更新時もアップロード可能にしたい場合）
        if ($request->hasFile('img_url')) {
            $validated['img_url'] = $request->file('img_url')->store('profiles', 'public');
            $user->profile->update(['img_url' => $validated['img_url']]);
        }
    
        // プロフィール情報は profiles テーブル
        $user->profile->update([
            'postcode' => $validated['postcode'],
            'address'  => $validated['address'],
            'building' => $validated['building'],
        ]);

        return redirect()->route('mypage.show')->with('success', 'プロフィールを更新しました');
        }

        public function purchased()
        {
            $user = auth()->user();

            $purchasedItems = $user->soldItems()->with('item')->get();

            return view('profile.purchased', compact('purchasedItems'));
        }


}
