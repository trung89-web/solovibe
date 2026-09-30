<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_code',
        'receiver_name',
        'receiver_phone',
        'province',
        'district',
        'ward',
        'specific_address',
        'shipping_fee',
        'total_amount',
        'payment_method',
        'payment_status',
        'order_status',
        'note',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function paymentTransactions()
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function getAllowedNextStatuses(): array
    {
        $statusFlow = [
            'pending' => ['pending', 'processing'],
            'processing' => ['processing', 'packed'],
            'packed' => ['packed', 'shipping'],
            'shipping' => ['shipping', 'completed'],
            'completed' => ['completed'],
            'cancelled' => ['cancelled'],
            'returned' => ['returned'],
        ];

        return $statusFlow[$this->order_status] ?? [$this->order_status];
    }
}

