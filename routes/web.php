<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ExportController;

Route::get('/', fn() => redirect()->route('dashboard'));

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');
    Route::get('/products', fn() => view('products.index'))->name('products.index');
    Route::get('/customers', fn() => view('customers.index'))->name('customers.index');
    Route::get('/sales', fn() => view('sales.index'))->name('sales.index');
    Route::get('/sales/create', fn() => view('sales.create'))->name('sales.create');
    Route::get('/sales/{id}/invoice', [InvoiceController::class, 'show'])->name('sales.invoice');
    Route::get('/debts', fn() => view('debts.index'))->name('debts.index');
    Route::get('/reports', fn() => view('reports.index'))->name('reports.index');
    Route::get('/reports/export', [ExportController::class, 'excel'])->name('reports.export');
    Route::get('/reports/pdf', [ExportController::class, 'pdf'])->name('reports.pdf');
    Route::get('/settings', fn() => view('settings.index'))->name('settings.index');
    Route::get('/profile', fn() => view('profile'))->name('profile.edit');
});

Route::middleware('auth')->post('/logout', function (\Illuminate\Http\Request $request) {
    auth()->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

require __DIR__.'/auth.php';
