<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Item;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class CommentController extends Controller
{
    //
    public function store(CommentRequest $request, $itemId)
    {
        $item = Item::findOrFail($itemId);

        Comment::create([
            'item_id' => $item->id,
            'user_id' => Auth::id(),
            'comment' => $request->comment,
        ]);

        return redirect()
            ->route('items.show', $item->id)
            ->with('success', 'コメントを投稿しました');
    }
}
