<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number', 'table_id', 'waiter_id', 'cashier_id',
        'customer_id', 'order_type', 'status', 'subtotal',
        'tax_amount', 'discount_amount', 'total_amount', 'notes'
    ];

    public function table()
    {
        return $this->belongsTo(DiningTable::class, 'table_id');
    }

    public function waiter()
    {
        return $this->belongsTo(User::class, 'waiter_id');
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function kdsTicket()
    {
        return $this->hasOne(KdsTicket::class);
    }
}
