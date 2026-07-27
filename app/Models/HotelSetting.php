<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelSetting extends Model
{
    protected $fillable = [
        'hotel_name', 'branch_code', 'contact_phone', 'contact_email',
        'gst_vat_number', 'currency_symbol', 'default_tax_rate',
        'kds_enabled', 'kds_routing_mode', 'kitchen_printer_ip',
        'kitchen_printer_port', 'cashier_printer_ip', 'auto_deduct_inventory_on_print'
    ];

    protected $casts = [
        'kds_enabled' => 'boolean',
        'auto_deduct_inventory_on_print' => 'boolean',
        'default_tax_rate' => 'float',
        'kitchen_printer_port' => 'integer',
    ];

    /**
     * Get active hotel operational settings singleton
     */
    public static function current(): self
    {
        $setting = self::first();
        if (!$setting) {
            $setting = self::create([
                'hotel_name' => 'Antigravity Grand Hotel & Gourmet Suite',
                'branch_code' => 'BR-MAIN-01',
                'contact_phone' => '+91 98765 43210',
                'contact_email' => 'management@antigravityhotel.com',
                'gst_vat_number' => '29AAFGA1234F1Z9',
                'currency_symbol' => '₹',
                'default_tax_rate' => 5.00,
                'kds_enabled' => false,
                'kds_routing_mode' => 'thermal_printer_only',
                'kitchen_printer_ip' => '192.168.32.151',
                'kitchen_printer_port' => 9100,
                'cashier_printer_ip' => '192.168.32.150',
                'auto_deduct_inventory_on_print' => true,
            ]);
        }
        return $setting;
    }
}
