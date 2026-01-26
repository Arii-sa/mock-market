<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\TransactionCompletedMail;
use App\Models\Transaction;
use App\Models\SoldItem;
use App\Enums\ItemStatus;

class TransactionController extends Controller
{
    public function show(Transaction $transaction)
    {
        $user = Auth::user();

        if ($user->id !== $transaction->buyer_id && $user->id !== $transaction->seller_id) {
            abort(403);
        }

        $messages = $transaction->messages()
            ->withTrashed()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        $lastMessage = $messages->last();
        if ($lastMessage) {
            \App\Models\TransactionMessageRead::updateOrCreate(
                [
                    'transaction_id' => $transaction->id,
                    'user_id' => $user->id,
                ],
                [   'last_read_message_id' => $lastMessage->id]
            );
        }

        $otherTransactions = Transaction::whereIn('status', ['trading', 'completed'])
            ->where(function ($q) use ($user) {
                $q->where('buyer_id', $user->id)
                ->orWhere('seller_id', $user->id);
            })
            ->where('id', '!=', $transaction->id)
            ->with(['item', 'messages' => function ($q) {
                $q->latest();
            }])
            ->get()
            ->sortByDesc(function ($t) {
                return optional($t->messages->first())->created_at ?? $t->updated_at;
            });

        $showEvaluationModal = false;

        if ($transaction->status === 'completed') {
            // buyer
            if ($user->id === $transaction->buyer_id && !$transaction->buyerEvaluated) {
                $showEvaluationModal = true;
            }

            // seller
            if ($user->id === $transaction->seller_id && !$transaction->sellerEvaluated) {
                $showEvaluationModal = true;
            }
        }

        $editingMessageId = request('edit_message_id');

        // 表示するBladeを切り替え
        return view(
            $user->id === $transaction->buyer_id
                ? 'transactions.buyer'
                : 'transactions.seller',
            compact('transaction',
                    'messages',
                    'showEvaluationModal',
                    'otherTransactions',
                    'editingMessageId')
        );
    }

    public function complete(Request $request, Transaction $transaction)
    {
        $user = Auth::user();

        if ($user->id !== $transaction->buyer_id) {
            abort(403);
        }

        if ($transaction->status !== 'trading') {
            return redirect()->route('transactions.show', $transaction);
        }

        $transaction->update([
            'status' => 'completed',
        ]);


        $transaction->markAsFullyCompletedIfPossible();

        SoldItem::firstOrCreate(
            [
            'user_id' => $transaction->buyer_id,
            'item_id' => $transaction->item_id,],
            [
            'sending_postcode' => $transaction->buyer->profile->postcode ?? '',
            'sending_address'  => $transaction->buyer->profile->address ?? '',
            'sending_building' => $transaction->buyer->profile->building ?? '',
        ]);


        Mail::to($transaction->seller->email)
        ->send(new TransactionCompletedMail($transaction));

        return redirect()
        ->route('transactions.show', $transaction)
        ->with('showEvaluationModal', true);
    }

}

