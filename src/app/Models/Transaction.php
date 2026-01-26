<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\ItemStatus;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'buyer_id',
        'seller_id',
        'status',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function messages()
    {
        return $this->hasMany(TransactionMessage::class)
            ->orderBy('created_at');
    }

    public function messageReads()
    {
        return $this->hasMany(TransactionMessageRead::class);
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }

    public function getBuyerEvaluatedAttribute()
    {
        return $this->evaluations()
            ->where('from_user_id', $this->buyer_id)
            ->exists();
    }

    public function getSellerEvaluatedAttribute()
    {
        return $this->evaluations()
            ->where('from_user_id', $this->seller_id)
            ->exists();
    }

    public function markAsFullyCompletedIfPossible(): void
    {
        if (
            $this->status === 'completed' &&
            $this->buyerEvaluated &&
            $this->sellerEvaluated
        ) {
            $this->update([
                'status' => 'fully_completed',
            ]);

            $this->item->update([
                'status' => ItemStatus::SOLD,
            ]);
        }
    }

}
