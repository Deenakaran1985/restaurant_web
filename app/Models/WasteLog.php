<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WasteLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'incident_reference',
        'inventory_item_id',
        'item_name',
        'quantity',
        'unit',
        'unit_cost',
        'total_loss',
        'reason',
        'station',
        'logged_by'
    ];

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }
}
