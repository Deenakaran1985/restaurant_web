<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BanquetContract extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_number',
        'event_title',
        'client_name',
        'event_date',
        'venue_hall',
        'guest_count',
        'total_amount',
        'advance_paid',
        'status',
        'catering_notes'
    ];
}
