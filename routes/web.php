<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/switch-role/{role}', [DashboardController::class, 'switchRole'])->name('switch.role');

// Operational Touch UI & KDS Streams
Route::get('/pos', [DashboardController::class, 'pos'])->name('pos.index');
Route::post('/pos/order', [DashboardController::class, 'storePosOrder'])->name('pos.store_order');
Route::get('/kds', [DashboardController::class, 'kds'])->name('kds.index');
Route::get('/kds/ai-advisor', [\App\Http\Controllers\KitchenAiAdvisorController::class, 'index'])->name('kds.advisor');
Route::post('/kds/{id}/status/{status}', [DashboardController::class, 'updateKdsStatus'])->name('kds.update_status');
Route::post('/kds/simulate', [DashboardController::class, 'simulateKdsTicket'])->name('kds.simulate');
Route::post('/kds/clean-all', [DashboardController::class, 'cleanAllKds'])->name('kds.clean_all');
Route::get('/tables', [DashboardController::class, 'tables'])->name('tables.index');
Route::get('/tables/config', [\App\Http\Controllers\TableConfigController::class, 'index'])->name('tables.config');
Route::post('/tables/config', [\App\Http\Controllers\TableConfigController::class, 'store'])->name('tables.store');
Route::post('/tables/config/{id}/status', [\App\Http\Controllers\TableConfigController::class, 'updateStatus'])->name('tables.update_status');
Route::post('/tables/config/{id}/serve-and-invoice', [\App\Http\Controllers\TableConfigController::class, 'serveAndInvoice'])->name('tables.serve_invoice');
Route::post('/tables/config/{id}/update', [\App\Http\Controllers\TableConfigController::class, 'update'])->name('tables.update');
Route::post('/tables/config/{id}/delete', [\App\Http\Controllers\TableConfigController::class, 'destroy'])->name('tables.destroy');
Route::get('/delivery/dispatch', [\App\Http\Controllers\DeliveryController::class, 'index'])->name('delivery.index');
Route::post('/delivery/simulate-order', [\App\Http\Controllers\DeliveryController::class, 'simulateWebhook'])->name('delivery.simulate');
Route::get('/hotel/banquet-and-rooms', [\App\Http\Controllers\HotelCateringController::class, 'index'])->name('hotel.catering');
Route::post('/hotel/room-service/bill', [\App\Http\Controllers\HotelCateringController::class, 'billToRoom'])->name('hotel.bill_room');

// Inventory, COGS & Supplier Management
Route::get('/menu', [DashboardController::class, 'menu'])->name('menu.index');
Route::get('/menu/profit-engineering', [\App\Http\Controllers\MenuEngineeringController::class, 'index'])->name('menu.engineering');
Route::post('/menu/recipe', [\App\Http\Controllers\RecipeController::class, 'store'])->name('menu.store_recipe');
Route::post('/menu/category', [\App\Http\Controllers\RecipeController::class, 'storeCategory'])->name('menu.store_category');
Route::post('/menu/{id}/adjust-recipe', [\App\Http\Controllers\RecipeController::class, 'adjust'])->name('menu.adjust_recipe');
Route::get('/inventory', [DashboardController::class, 'inventory'])->name('inventory.index');
Route::get('/suppliers', [DashboardController::class, 'suppliers'])->name('suppliers.index');
Route::get('/inventory/waste-and-spillage', [\App\Http\Controllers\WasteController::class, 'index'])->name('inventory.waste');
Route::post('/inventory/waste-and-spillage/log', [\App\Http\Controllers\WasteController::class, 'logWaste'])->name('waste.log');

// Accounting & IT Admin Portals
Route::get('/accounts/invoices', [DashboardController::class, 'invoices'])->name('accounts.invoices');
Route::post('/accounts/invoices/{id}/settle', [DashboardController::class, 'settleInvoice'])->name('invoices.settle');
Route::get('/accounts/invoices/export', [DashboardController::class, 'exportLedgerCsv'])->name('invoices.export');
Route::get('/accounts/profit-and-loss', [\App\Http\Controllers\FinancialController::class, 'profitAndLoss'])->name('accounts.pl');
Route::get('/accounts/verify-lifecycle', [\App\Http\Controllers\FinancialController::class, 'verifyLifecycle'])->name('accounts.verify_lifecycle');
Route::get('/accounts/night-audit', [\App\Http\Controllers\NightAuditController::class, 'index'])->name('accounts.night_audit');
Route::post('/accounts/night-audit/execute', [\App\Http\Controllers\NightAuditController::class, 'executeAudit'])->name('night_audit.execute');
Route::get('/users', [DashboardController::class, 'users'])->name('users.index');
Route::get('/crm/patrons', [\App\Http\Controllers\CrmController::class, 'index'])->name('crm.index');
Route::post('/crm/patrons', [\App\Http\Controllers\CrmController::class, 'store'])->name('crm.store');
Route::post('/crm/patrons/{id}/bonus', [\App\Http\Controllers\CrmController::class, 'awardBonus'])->name('crm.award_bonus');
Route::get('/it-admin', [DashboardController::class, 'itAdmin'])->name('it_admin.index');
Route::post('/it-admin/printer-test', [DashboardController::class, 'printTestReceipt'])->name('printer.test');

// Hotel Configuration & KDS Mode Routing Suite
Route::get('/settings', [\App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
Route::post('/settings', [\App\Http\Controllers\SettingsController::class, 'update'])->name('settings.update');

// Guest Digital QR Code Table-side Self-Ordering Mobile Portal
Route::get('/menu/qr/{table_id}', [\App\Http\Controllers\QrOrderingController::class, 'showTableMenu'])->name('qr.menu');
Route::post('/menu/qr/{table_id}/order', [\App\Http\Controllers\QrOrderingController::class, 'placeGuestOrder'])->name('qr.order');
