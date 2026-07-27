<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id', 'name', 'sku', 'unit', 'current_stock',
        'min_alert_stock', 'unit_cost', 'preferred_supplier_id'
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'preferred_supplier_id');
    }

    public function stockLogs()
    {
        return $this->hasMany(StockLog::class);
    }
}
