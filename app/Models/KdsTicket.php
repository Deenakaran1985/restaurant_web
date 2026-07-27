<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KdsTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'ticket_number', 'station_name',
        'status', 'received_at', 'prepared_at', 'ready_at'
    ];

    protected $casts = [
        'received_at' => 'datetime',
        'prepared_at' => 'datetime',
        'ready_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
