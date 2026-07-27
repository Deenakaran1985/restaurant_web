<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RestaurantApiController;

/*
|--------------------------------------------------------------------------
| API Routes for Antigravity Restaurant Flutter Mobile & Tablet POS/KDS
| Endpoint Base: http://192.168.32.249:8107/api
|--------------------------------------------------------------------------
*/

// Fast Switch Touch PIN Authentication for mobile devices
Route::post('/auth/login-pin', [RestaurantApiController::class, 'loginWithPin']);

// Public operational endpoints for local network tablet kiosks
Route::get('/menu', [RestaurantApiController::class, 'getMenu']);
Route::get('/tables', [RestaurantApiController::class, 'getTables']);
Route::post('/tables/{id}/status', [RestaurantApiController::class, 'updateTableStatus']);

// KOT Order creation & Real-time KDS Stream
Route::post('/orders', [RestaurantApiController::class, 'placeOrder']);
Route::get('/kds/tickets', [RestaurantApiController::class, 'getKdsTickets']);
Route::post('/kds/tickets/{id}/status', [RestaurantApiController::class, 'updateKdsTicketStatus']);

// Storekeeper Raw Material Inventory Scanner APIs
Route::get('/inventory', [RestaurantApiController::class, 'getInventory']);
Route::post('/inventory/{id}/adjust', [RestaurantApiController::class, 'adjustStock']);

// Hardware Thermal LAN Printer Diagnostics API (Zero CSRF barrier for POS handhelds)
Route::post('/it-admin/printer-test', [\App\Http\Controllers\DashboardController::class, 'printTestReceipt']);

// Live Hotel Operational Profile & KDS Mode Sync for Flutter Mobile App
Route::get('/settings', function() {
    return response()->json(['success' => true, 'settings' => \App\Models\HotelSetting::current()]);
});

// Guest Digital QR Table-side Self-Ordering API
Route::post('/menu/qr/{table_id}/order', [\App\Http\Controllers\QrOrderingController::class, 'placeGuestOrder']);

// Mobile POS Tablet & Guest QR Patron VIP Loyalty Lookup API
Route::get('/patrons', function(Request $request) {
    $phone = $request->query('phone');
    if ($phone) {
        return response()->json(['success' => true, 'patron' => \App\Models\Patron::where('phone', 'like', "%{$phone}%")->first()]);
    }
    return response()->json(['success' => true, 'patrons' => \App\Models\Patron::where('is_active', true)->get()]);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
