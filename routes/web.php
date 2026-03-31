<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    DashboardController,
    PosController,
    ClientController,
    InvoiceController,
    QuotationController,
    ProductController,
    BarcodeController,
};
use App\Http\Controllers\Account\{IncomeController, ExpenseController};
use App\Http\Controllers\Report\InvoiceReportController;
use App\Http\Controllers\Admin\{RoleController, UnitController, SettingController, BackupController};

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // POS
    Route::prefix('pos')->name('pos.')->group(function () {
        Route::get('/',                [PosController::class, 'index'])->name('index');
        Route::post('/store',          [PosController::class, 'store'])->name('store');
        Route::get('/receipt/{invoice}',[PosController::class, 'receipt'])->name('receipt');
        Route::get('/search-product',  [PosController::class, 'searchProduct'])->name('search-product');
        Route::get('/scan-barcode',    [PosController::class, 'searchByBarcode'])->name('scan-barcode');
        Route::post('/checkout',            [PosController::class, 'checkout'])->name('checkout');
        Route::get('/download/{invoice}', [PosController::class, 'download'])->name('download');
    });

    // Clients
    Route::resource('client', ClientController::class);

    Route::prefix('clients')->name('clients.')->group(function () {
        Route::get('/', [ClientController::class, 'index'])->name('index');
        Route::get('/create', [ClientController::class, 'create'])->name('create');
        Route::post('/', [ClientController::class, 'store'])->name('store');
        Route::get('/{client}/edit', [ClientController::class, 'edit'])->name('edit');
        Route::put('/{client}', [ClientController::class, 'update'])->name('update');
        Route::delete('/{client}', [ClientController::class, 'destroy'])->name('destroy');
        Route::get('/ledger/{client}', [ClientController::class, 'ledger'])->name('ledger');
    });

    // Invoices
    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/',          [InvoiceController::class, 'index'])->name('index');
        Route::get('/create',    [InvoiceController::class, 'create'])->name('create');
        Route::post('/',         [InvoiceController::class, 'store'])->name('store');
        Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('show');
        Route::delete('/{invoice}', [InvoiceController::class, 'destroy'])->name('destroy');
        Route::get('/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('pdf');
    });

    // Quotations
    Route::prefix('quotations')->name('quotations.')->group(function () {
        Route::get('/',             [QuotationController::class, 'index'])->name('index');
        Route::get('/create',       [QuotationController::class, 'create'])->name('create');
        Route::post('/',            [QuotationController::class, 'store'])->name('store');
        Route::get('/{quotation}',  [QuotationController::class, 'show'])->name('show');
        Route::delete('/{quotation}',[QuotationController::class, 'destroy'])->name('destroy');
        Route::get('/{quotation}/pdf', [QuotationController::class, 'pdf'])->name('pdf');
        Route::post('/{quotation}/convert', [QuotationController::class, 'convertToInvoice'])->name('convert');
    });

    // Products
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/',          [ProductController::class, 'index'])->name('index');
        Route::get('/create',    [ProductController::class, 'create'])->name('create');
        Route::post('/',         [ProductController::class, 'store'])->name('store');
        Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
        Route::put('/{product}', [ProductController::class, 'update'])->name('update');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
    });
    Route::resource('product', ProductController::class);
    Route::get('/product-scan', [ProductController::class, 'searchByBarcode'])->name('product.scan');

    // Barcode
    Route::prefix('barcodes')->name('barcodes.')->group(function () {
        Route::get('/',          [BarcodeController::class, 'index'])->name('index');
        Route::post('/generate', [BarcodeController::class, 'generate'])->name('generate');
        Route::get('/print/{id}', [BarcodeController::class, 'print'])->name('print');
        Route::get('/lookup',    [BarcodeController::class, 'lookup'])->name('lookup');
    });

    // Account
    Route::prefix('account')->group(function () {
        Route::resource('income',  IncomeController::class);
        Route::resource('expense', ExpenseController::class);
    });

    // Reports
    Route::prefix('report')->name('report.')->group(function () {
        Route::get('/invoice',     [InvoiceReportController::class, 'index'])->name('invoice');
        Route::get('/invoice/pdf', [InvoiceReportController::class, 'pdf'])->name('invoice.pdf');
    });

    // ── Admin routes (Admin role only) ───────────────────────
    Route::middleware(['role:Admin'])->group(function () {
        Route::resource('roles', RoleController::class);
        Route::resource('units', UnitController::class);
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/',    [SettingController::class, 'index'])->name('index');
            Route::post('/',   [SettingController::class, 'update'])->name('update');
        });
        Route::prefix('backup')->name('backup.')->group(function () {
            Route::get('/',              [BackupController::class, 'index'])->name('index');
            Route::post('/create',       [BackupController::class, 'create'])->name('create');
            Route::get('/download/{filename}', [BackupController::class, 'download'])->name('download');
            Route::delete('/{id}',       [BackupController::class, 'destroy'])->name('destroy');
        });
    });

    // ── User management ──────────────────────────────────────
    /* Route::middleware(['role:Admin'])->prefix('users')->name('users.')->group(function () {
        Route::get('/',         [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('index');
        Route::get('/create',   [\App\Http\Controllers\Admin\UserController::class, 'create'])->name('create');
        Route::post('/',        [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('edit');
        Route::put('/{user}',   [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('update');
        Route::delete('/{user}',[\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('destroy');
    }); */
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
