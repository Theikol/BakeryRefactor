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

    protected $appends = ['order_code', 'status_label'];

    // relasi ke order items
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    // Accessor untuk order_code
    public function getOrderCodeAttribute()
    {
        return $this->order_number ?? 'LMN-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    // Accessor untuk status_label (translated status)
    public function getStatusLabelAttribute()
    {
        $statusMap = [
            'pending' => 'Pending',
            'waiting_confirmation' => 'Menunggu Konfirmasi',
            'processing' => 'Diproses',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            'paid' => 'Sudah Dibayar',
        ];

        return $statusMap[$this->status] ?? ucfirst($this->status);
    }
}