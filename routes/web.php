<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\PwaController;

// ── PWA (public — no auth required) ───────────────────────
Route::get('/manifest.json', [PwaController::class, 'manifest'])->name('pwa.manifest');
Route::get('/sw.js',         [PwaController::class, 'sw'])->name('pwa.sw');
Route::get('/offline',       [PwaController::class, 'offline'])->name('pwa.offline');
Route::get('/pwa/icon/{size}', [PwaController::class, 'icon'])
    ->where('size', '192|512')
    ->name('pwa.icon');

Route::get('/', fn() => redirect()->route('dashboard'));

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');
    Route::get('/profile', fn() => view('profile'))->name('profile.edit');

    // Kasir & Admin
    Route::get('/sales/create', fn() => view('sales.create'))->name('sales.create');
    Route::get('/sales', fn() => view('sales.index'))->name('sales.index');
    Route::get('/sales/{id}/invoice', [InvoiceController::class, 'show'])->name('sales.invoice');
    Route::get('/sales/{id}/invoice-customer', [InvoiceController::class, 'showCustomer'])->name('sales.invoice-customer');
    Route::get('/sales/{id}/invoice-excel', [ExportController::class, 'invoiceExcel'])->name('sales.invoice-excel');
    Route::get('/sales/export-customer', [ExportController::class, 'customerExcel'])->name('sales.export-customer');
    Route::get('/sales/export-customer-pdf', [ExportController::class, 'customerPdf'])->name('sales.export-customer-pdf');

    // Admin only
    Route::middleware('admin')->group(function () {
        Route::get('/products', fn() => view('products.index'))->name('products.index');
        Route::get('/products/export', [ExportController::class, 'productExport'])->name('products.export');
        Route::get('/products/import-template', [ExportController::class, 'productImportTemplate'])->name('products.import-template');
        Route::get('/customers', fn() => view('customers.index'))->name('customers.index');
        Route::get('/debts', fn() => view('debts.index'))->name('debts.index');
        Route::get('/reports', fn() => view('reports.index'))->name('reports.index');
        Route::get('/reports/export', [ExportController::class, 'excel'])->name('reports.export');
        Route::get('/reports/pdf', [ExportController::class, 'pdf'])->name('reports.pdf');
        Route::get('/reports/stock-export', [ExportController::class, 'stockExcel'])->name('reports.stock-export');
        Route::get('/users', fn() => view('users.index'))->name('users.index');
        Route::get('/settings', fn() => view('settings.index'))->name('settings.index');
        Route::get('/backup', fn() => view('backup.index'))->name('backup.index');
        Route::get('/backup/create', [BackupController::class, 'create'])->name('backup.create');
        Route::get('/backup/download/{filename}', [BackupController::class, 'download'])->name('backup.download');
        Route::post('/backup/import', [BackupController::class, 'import'])->name('backup.import');
        Route::delete('/backup/delete/{filename}', [BackupController::class, 'delete'])->name('backup.delete');
    });
});

Route::middleware('auth')->post('/logout', function (\Illuminate\Http\Request $request) {
    auth()->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

require __DIR__.'/auth.php';
