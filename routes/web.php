<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Products\Index as ProductsIndex;
use App\Livewire\RawMaterials\Index as RawMaterialsIndex;
use App\Livewire\StockInputs\Index as StockInputsIndex;
use App\Livewire\StockOpnames\Index as StockOpnamesIndex;
use App\Livewire\Transactions\Index as TransactionsIndex;

Route::middleware(['auth'])->group(function () {
    Route::get('/', \App\Livewire\Dashboard::class)->name('dashboard');
    Route::get('/change-password', \App\Livewire\ChangePassword::class)->name('change-password');
    Route::get('/transactions', TransactionsIndex::class)->name('transactions.index');
    Route::get('/transactions/{id}/print', [\App\Http\Controllers\TransactionPrintController::class, 'show'])->name('transactions.print');

    Route::middleware(['admin'])->group(function () {
        Route::get('/users', \App\Livewire\Users\Index::class)->name('users.index');
        Route::get('/products', ProductsIndex::class)->name('products.index');
        Route::get('/products/{id}', \App\Livewire\Products\Detail::class)->name('products.detail');
        Route::get('/raw-materials', RawMaterialsIndex::class)->name('raw-materials.index');
        Route::get('/raw-materials/{id}', \App\Livewire\RawMaterials\Detail::class)->name('raw-materials.detail');
        Route::get('/raw-material-stock-inputs', StockInputsIndex::class)->name('raw-material-stock-inputs.index');
        Route::get('/raw-material-stock-opnames', StockOpnamesIndex::class)->name('raw-material-stock-opnames.index');
        Route::get('/reports/by-products', \App\Livewire\Reports\ByProducts::class)->name('reports.by-products');
        Route::get('/reports/by-transactions', \App\Livewire\Reports\ByTransactions::class)->name('reports.by-transactions');
    });
});

require __DIR__.'/auth.php';
