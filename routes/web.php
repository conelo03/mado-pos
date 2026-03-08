<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Transactions\Index as TransactionsIndex;

Route::middleware(['auth'])->group(function () {
    Route::get('/', \App\Livewire\Dashboard::class)->name('dashboard');
    Route::get('/change-password', \App\Livewire\ChangePassword::class)->name('change-password');
    Route::get('/transactions', TransactionsIndex::class)->name('transactions.index');
    Route::get('/transactions/{id}/print', [\App\Http\Controllers\TransactionPrintController::class, 'show'])->name('transactions.print');

    Route::middleware(['admin'])->group(function () {
        Route::get('/users', \App\Livewire\Users\Index::class)->name('users.index');
        Route::get('/items', \App\Livewire\Items\Index::class)->name('items.index');
        Route::get('/items/{id}', \App\Livewire\Items\Detail::class)->name('items.detail');
        Route::get('/stock-input', \App\Livewire\StockManagement\StockInput::class)->name('stock-input.index');
        Route::get('/stock-opname', \App\Livewire\StockManagement\StockOpname::class)->name('stock-opname.index');
        Route::get('/reports/by-products', \App\Livewire\Reports\ByProducts::class)->name('reports.by-products');
        Route::get('/reports/by-transactions', \App\Livewire\Reports\ByTransactions::class)->name('reports.by-transactions');
    });
});

require __DIR__.'/auth.php';
