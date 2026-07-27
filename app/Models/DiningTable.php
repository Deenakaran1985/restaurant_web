<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiningTable extends Model
{
    use HasFactory;

    protected $fillable = [
        'floor_section_id', 'table_number', 'capacity', 'status', 'current_order_id'
    ];

    public function section()
    {
        return $this->belongsTo(FloorSection::class, 'floor_section_id');
    }

    public function currentOrder()
    {
        return $this->belongsTo(Order::class, 'current_order_id');
    }
}
