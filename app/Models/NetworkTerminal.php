<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NetworkTerminal extends Model
{
    use HasFactory;

    protected $fillable = ['terminal_name', 'ip_address', 'terminal_type', 'is_authorized', 'last_ping_at'];

    protected $casts = ['last_ping_at' => 'datetime'];
}
