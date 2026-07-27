<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patron extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'tier',
        'loyalty_points',
        'lifetime_spend',
        'favorite_dish_category',
        'dietary_notes',
        'is_active',
    ];

    /**
     * Award loyalty points based on order total and promote tier if spend exceeds thresholds
     */
    public function awardPointsAndPromote(float $orderTotal): void
    {
        // 1 point per 100 currency units spent
        $earnedPoints = (int) floor($orderTotal / 100);
        
        $this->loyalty_points += $earnedPoints;
        $this->lifetime_spend += $orderTotal;

        // Auto Tier Progression
        if ($this->lifetime_spend >= 25000) {
            $this->tier = 'platinum_vip';
        } elseif ($this->lifetime_spend >= 10000) {
            $this->tier = 'gold';
        } else {
            $this->tier = 'silver';
        }

        $this->save();
    }
}
