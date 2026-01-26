<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\TransactionMessageRequest;
use App\Models\Transaction;
use App\Models\TransactionMessage;

class TransactionMessageController extends Controller
{
    public function store(TransactionMessageRequest $request, Transaction $transaction)
    {
        TransactionMessage::create([
            'transaction_id' => $transaction->id,
            'user_id' => Auth::id(),
            'body' => $request->body,
            'image_path' => $request->hasFile('image')
                ? $request->file('image')->store('messages', 'public')
                : null,
        ]);

        $transaction->touch();

        return redirect()->route('transactions.show', $transaction);
    }

    public function update(Request $request, Transaction $transaction, TransactionMessage $message)
    {
        // 自分のメッセージ以外は編集不可
        if ($message->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'body' => ['required', 'string', 'max:1000'],
        ]);

        $message->update([
            'body' => $request->body,
            'edited_at' => now(),
        ]);

        return redirect()
            ->route('transactions.show', $transaction);
    }

    /**
     * メッセージ削除
     */
    public function destroy(Transaction $transaction, TransactionMessage $message)
    {
        if ($message->user_id !== Auth::id()) {
            abort(403);
        }

        $message->delete();

        return redirect()
            ->route('transactions.show', $transaction);
    }
}
