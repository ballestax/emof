<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\FileController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

#Route::get('files', [FileController::class,'index'])->name('files.index');
#Route::post('files', [FileController::class,'storeFile'])->name('files.index');
#Route::get('/files/{id}', [FileController::class, 'show'])->name('files.show');

Route::resource('files', FileController::class)
        ->only('index', 'create', 'show', 'edit');


