<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HotelSetting;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    /**
     * Display Hotel & KDS Configuration Portal
     */
    public function index()
    {
        $settings = HotelSetting::current();
        return view('settings.index', compact('settings'));
    }

    /**
     * Update Hotel Profile & Optional KDS Mode Toggles
     */
    public function update(Request $request)
    {
        $request->validate([
            'hotel_name' => 'required|string|max:255',
            'branch_code' => 'required|string|max:50',
            'gst_vat_number' => 'required|string|max:100',
            'currency_symbol' => 'required|string|max:10',
            'default_tax_rate' => 'required|numeric|min:0|max:100',
            'kds_routing_mode' => 'required|in:thermal_printer_only,screen_interactive',
            'kitchen_printer_ip' => 'required|ip',
            'kitchen_printer_port' => 'required|integer',
            'cashier_printer_ip' => 'required|ip',
        ]);

        $settings = HotelSetting::current();
        
        $kdsEnabled = $request->kds_routing_mode === 'screen_interactive';

        $settings->update([
            'hotel_name' => $request->hotel_name,
            'branch_code' => $request->branch_code,
            'contact_phone' => $request->contact_phone,
            'contact_email' => $request->contact_email,
            'gst_vat_number' => $request->gst_vat_number,
            'currency_symbol' => $request->currency_symbol,
            'default_tax_rate' => $request->default_tax_rate,
            'kds_routing_mode' => $request->kds_routing_mode,
            'kds_enabled' => $kdsEnabled,
            'kitchen_printer_ip' => $request->kitchen_printer_ip,
            'kitchen_printer_port' => $request->kitchen_printer_port,
            'cashier_printer_ip' => $request->cashier_printer_ip,
            'auto_deduct_inventory_on_print' => $request->has('auto_deduct_inventory_on_print'),
        ]);

        Log::info("Hotel & KDS Operational Settings updated by user ID: " . auth()->id());

        return redirect()->route('settings.index')->with('success', 'Hotel operational profile and KDS thermal routing mode updated successfully!');
    }
}
