<?php

use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\StreamController;

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
// Route::get('/stream/{filename}', [StreamController::class, 'stream'])->name('stream');



