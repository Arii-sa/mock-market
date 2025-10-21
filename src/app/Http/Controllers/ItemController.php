<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Http\Requests\ExhibitionRequest;
use App\Models\Condition;

class ItemController extends Controller
{
    // トップ（一覧 / マイリスト 切替）
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'items'); // 'items' or 'mylist'
        $keyword = $request->input('keyword');

        if ($tab === 'mylist') {
            // マイリスト（いいね商品）処理
            if (!Auth::check()) {
                  $items = collect(); // 未ログインなら空コレクション
            } else {
                // likedItems() が User モデルで belongsToMany(Item::class, 'likes', 'user_id', 'item_id') を返す前提
                $query = Auth::user()->likedItems()
                    ->with(['categories','condition'])
                    ->withCount(['likes','comments'])
                    // ← ここで明示的に items.user_id を使う（曖昧さを解消）
                    ->where('items.user_id', '!=', Auth::id());

                if ($keyword) {
                    $query->where('name', 'like', "%{$keyword}%");
                }

                $items = $query->get();
            }
        } else {
              // 通常一覧
            $query = Item::with(['categories','condition'])
                ->withCount(['likes','comments']);

            if (Auth::check()) {
                $query->where('user_id', '!=', Auth::id());
            }

            if ($keyword) {
                $query->where('name', 'like', "%{$keyword}%");
            }

            $items = $query->get();
        }


        return view('items.index', compact('items', 'tab', 'keyword'));

    }




    public function show(Item $item)
    {
        // 必要な関連データをロード
        $item->load([
            'categories',
            'condition',
            'likes',
            'comments.user',
        ]);
    
        return view('items.detail', compact('item'));
    }
    
    public function create()
    {
        $categories = Category::all(); // カテゴリ一覧
        $conditions = Condition::all();

        return view('items.sell', compact('categories', 'conditions'));
    }

    public function store(ExhibitionRequest $request)
    {
        $validated = $request->validated();

        $validated['img_url'] = $request->file('img_url')->store('items', 'public');


        // 出品者情報を付与
        $validated['user_id'] = Auth::id();

        // 商品保存
        $item = Item::create($validated);

        // カテゴリ紐づけ
        $item->categories()->sync($validated['categories']);

        return redirect()->route('items.index')->with('success', '商品を出品しました！');
    }



}
