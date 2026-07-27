<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'location', 'manager_name', 'is_active'];

    public function inventoryItems()
    {
        return $this->hasMany(InventoryItem::class);
    }
}
