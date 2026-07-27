<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItemVariation extends Model
{
    use HasFactory;

    protected $fillable = ['menu_item_id', 'name', 'price_adjustment'];

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }
}
