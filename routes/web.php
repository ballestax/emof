<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\FileController;
use App\Livewire\Search\InvoiceSearch;
use App\Livewire\Search\InvoiceHistory;
use App\Livewire\Dashboard;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    // Descomenta y usa este grupo si estás usando Jetstream y quieres sus funcionalidades
    // O simplemente usa 'auth' si no usas Jetstream o para una protección más simple:
    Route::resource('files', FileController::class)
            ->only(['index', 'create', 'store', 'show']);

    Route::get('/search', InvoiceSearch::class)->name('invoices.search');

    Route::get('/history', InvoiceHistory::class)->name('invoices.history');

    Route::get('/dashboard', Dashboard::class)->name('dashboard');
});


Route::middleware(['auth'])->group(function () {
   

});
