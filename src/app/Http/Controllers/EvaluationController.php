<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use App\Models\Evaluation;
use App\Enums\ItemStatus;

class EvaluationController extends Controller
{
    public function store(Request $request, Transaction $transaction)
    {
        $user = Auth::user();

        $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
        ]);

        // 評価対象ユーザー判定
        $toUserId = $user->id === $transaction->buyer_id
            ? $transaction->seller_id
            : $transaction->buyer_id;

        // 二重評価防止
        Evaluation::firstOrCreate(
            [
                'transaction_id' => $transaction->id,
                'from_user_id' => $user->id,
            ],
            [
                'to_user_id' => $toUserId,
                'score' => $request->rating,
            ]
        );

        $transaction->markAsFullyCompletedIfPossible();

        return redirect()
            ->route('items.index')
            ->with('success', '評価を送信しました');
    }
}
