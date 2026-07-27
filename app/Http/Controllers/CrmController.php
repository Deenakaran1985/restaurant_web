<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patron;
use App\Models\HotelSetting;

class CrmController extends Controller
{
    /**
     * Display Patron CRM & VIP Loyalty Rewards Portal
     */
    public function index()
    {
        $settings = HotelSetting::current();
        
        // Auto-seed initial VIP patron profiles if empty
        if (Patron::count() === 0) {
            Patron::create([
                'name' => 'Rahul VIP Dining',
                'phone' => '+91-9876500001',
                'email' => 'rahul.vip@resort.com',
                'tier' => 'platinum_vip',
                'loyalty_points' => 1250,
                'lifetime_spend' => 45000.00,
                'favorite_dish_category' => 'Wood-Fired Pizzas',
                'dietary_notes' => 'Strictly vegetarian, no mushrooms',
                'is_active' => true,
            ]);
            Patron::create([
                'name' => 'Dr. Ananya Sharma',
                'phone' => '+91-9876500002',
                'email' => 'ananya@health.org',
                'tier' => 'gold',
                'loyalty_points' => 480,
                'lifetime_spend' => 14500.00,
                'favorite_dish_category' => 'Artisanal Coffee & Beverages',
                'dietary_notes' => 'Lactose intolerant, use Almond or Oat milk only',
                'is_active' => true,
            ]);
            Patron::create([
                'name' => 'Vikash Kumar',
                'phone' => '+91-9876500003',
                'email' => 'vikash.tech@suite.in',
                'tier' => 'silver',
                'loyalty_points' => 180,
                'lifetime_spend' => 3200.00,
                'favorite_dish_category' => 'Gourmet Burgers',
                'dietary_notes' => 'Halal certified meat preferences',
                'is_active' => true,
            ]);
        }

        $patrons = Patron::orderByDesc('loyalty_points')->get();
        
        $crmStats = [
            'total_patrons' => $patrons->count(),
            'total_points_in_circulation' => $patrons->sum('loyalty_points'),
            'total_lifetime_spend' => $patrons->sum('lifetime_spend'),
            'platinum_count' => $patrons->where('tier', 'platinum_vip')->count(),
            'gold_count' => $patrons->where('tier', 'gold')->count(),
        ];

        return view('crm.index', compact('patrons', 'crmStats', 'settings'));
    }

    /**
     * Award Bonus Loyalty Points to Patron (Anniversary / Service Goodwill)
     */
    public function awardBonus(Request $request, $id)
    {
        $request->validate([
            'bonus_points' => 'required|integer|min:10|max:5000',
            'reason' => 'required|string|max:255',
        ]);

        $patron = Patron::findOrFail($id);
        $patron->loyalty_points += intval($request->bonus_points);
        $patron->save();

        return redirect()->route('crm.index')->with('success', "Successfully awarded {$request->bonus_points} bonus VIP loyalty points to {$patron->name}!");
    }

    /**
     * Enroll Brand New VIP Guest into CRM Loyalty Ledger
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:patrons,phone|max:50',
            'email' => 'nullable|email|max:255',
            'tier' => 'required|string',
            'loyalty_points' => 'nullable|integer|min:0',
        ]);

        $patron = Patron::create([
            'name' => trim($request->input('name')),
            'phone' => trim($request->input('phone')),
            'email' => trim($request->input('email', '')),
            'tier' => $request->input('tier', 'silver'),
            'loyalty_points' => intval($request->input('loyalty_points', 100)),
            'lifetime_spend' => floatval($request->input('lifetime_spend', 0.00)),
            'favorite_dish_category' => $request->input('favorite_dish_category', 'Gourmet Specialties'),
            'dietary_notes' => $request->input('dietary_notes', ''),
            'is_active' => true,
        ]);

        return redirect()->route('crm.index')->with('success', "🎉 VIP Patron '{$patron->name}' has been successfully enrolled into the {$patron->tier} tier with an initial deposit of {$patron->loyalty_points} loyalty points!");
    }
}
