<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toIso8601String(),
        'environment' => app()->environment(),
    ]);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::get('/notifications/{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');

    Route::get('reports/leads', [ReportController::class, 'exportLeads'])->name('reports.leads')->middleware('permission:view reports');
    Route::get('reports/orders', [ReportController::class, 'exportOrders'])->name('reports.orders')->middleware('permission:view reports');

    Route::resource('leads', LeadController::class);
    Route::resource('orders', OrderController::class)->only(['index', 'store', 'show', 'edit', 'update', 'destroy']);
    Route::get('orders/{order}/invoice.pdf', [OrderController::class, 'downloadInvoice'])->name('orders.invoice');
    Route::get('leads/{lead}/orders/create', [OrderController::class, 'create'])->name('orders.create')->middleware('permission:create orders');

    Route::resource('products', App\Http\Controllers\ProductController::class)->middleware('permission:view products');

    Route::resource('campaigns', CampaignController::class)->middleware('permission:view campaigns');

    Route::group(['middleware' => ['role:admin']], function () {
        Route::resource('users', UserController::class);
        Route::resource('roles', App\Http\Controllers\RoleController::class);
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
