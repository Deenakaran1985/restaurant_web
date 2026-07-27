<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'contact_person', 'phone', 'email', 'address', 'gst_vat_number'
    ];

    public function inventoryItems()
    {
        return $this->hasMany(InventoryItem::class, 'preferred_supplier_id');
    }
}
