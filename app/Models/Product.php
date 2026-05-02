<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function orderItems()
{
    return $this->hasMany(OrderItem::class);
}

    protected $fillable = [
        'name',
        'category',
        'price',
        'image',
        'description',
        'rating',
        'stock',
        'is_active'
    ];
}
