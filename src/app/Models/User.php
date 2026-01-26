<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function likes()
    {
        return $this->hasMany(\App\Models\Like::class);
    }

    // likedItems (Item のコレクションを取得)
    public function likedItems()
    {
        // likes テーブルを中間テーブルとして items に紐付け
        return $this->belongsToMany(\App\Models\Item::class, 'likes', 'user_id', 'item_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function soldItems()
    {
        return $this->hasMany(SoldItem::class);
    }

    // 購入者としての取引
    public function boughtTransactions()
    {
        return $this->hasMany(Transaction::class, 'buyer_id');
    }

    // 出品者としての取引
    public function soldTransactions()
    {
        return $this->hasMany(Transaction::class, 'seller_id');
    }

    // 自分が送信した取引メッセージ
    public function transactionMessages()
    {
        return $this->hasMany(TransactionMessage::class);
    }

    // 自分が受け取った評価
    public function receivedEvaluations()
    {
        return $this->hasMany(Evaluation::class, 'to_user_id');
    }

    // 自分が送った評価
    public function givenEvaluations()
    {
        return $this->hasMany(Evaluation::class, 'from_user_id');
    }

    public function getAverageRatingAttribute()
    {
        $avg = $this->receivedEvaluations()->avg('score');

        return $avg ? round($avg) : null;
    }

}
