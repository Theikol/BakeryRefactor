<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'total_price',
        'payment_method',
        'payment_proof',
        'status',
        'payment_token',
        'payment_url'
    ];

    // relasi ke order items
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function items()
{
    return $this->hasMany(OrderItem::class, 'order_id');
}
}