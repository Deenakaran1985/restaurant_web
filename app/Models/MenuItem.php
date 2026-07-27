<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'name', 'code', 'description', 'price',
        'tax_percentage', 'image_url', 'prep_time_minutes', 'is_available'
    ];

    public function category()
    {
        return $this->belongsTo(MenuCategory::class, 'category_id');
    }

    public function variations()
    {
        return $this->hasMany(MenuItemVariation::class);
    }

    public function recipes()
    {
        return $this->hasMany(RecipeMapping::class);
    }

    public function ingredients()
    {
        return $this->belongsToMany(InventoryItem::class, 'recipe_mappings')
                    ->withPivot('quantity_needed');
    }
}
