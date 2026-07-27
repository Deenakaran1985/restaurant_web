<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FloorSection extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function tables()
    {
        return $this->hasMany(DiningTable::class);
    }
}
