<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\ItemStatus;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'condition_id',
        'name',
        'price',
        'brand',
        'description',
        'img_url',
        'status',
    ];

    protected $casts = [
        'status' => ItemStatus::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function condition()
    {
        return $this->belongsTo(Condition::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // soldItem (売却情報：1対1想定)
    public function soldItem()
    {
        return $this->hasOne(SoldItem::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_items', 'item_id', 'category_id');
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }

    public function isAvailable(): bool
    {
        return $this->status === ItemStatus::AVAILABLE;
    }

    public function isTrading(): bool
    {
        return $this->status === ItemStatus::TRADING;
    }

    public function isSold(): bool
    {
        return $this->status === ItemStatus::SOLD;
    }

}
