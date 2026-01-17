<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\SoldItem;
use App\Http\Requests\ProfileRequest;
use App\Models\Profile;

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
                ->with('item') // sold_items.item を一緒に取得
                ->get()
                ->pluck('item'); // Item モデルだけを抽出
        } else {
            // 出品した商品
            $items = $user->items()
                ->withCount(['likes', 'comments'])
                ->get();
        }

        return view('profiles.mypage', compact('user', 'items', 'tab', 'profile'));
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
