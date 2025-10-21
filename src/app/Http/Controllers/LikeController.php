<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    //
    public function store(Item $item)
    {
        $item->likes()->firstOrCreate([
            'user_id' => Auth::id(),
        ]);

        return back();
    }

    public function destroy(Item $item)
    {
        $item->likes()->where('user_id', Auth::id())->delete();

        return back();
    }
}
