<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MovieController;

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

Route::get('/movies/{movie}/edit', [MovieController::class, 'edit'])->name('movies.edit');
Route::put('/movies/update', [MovieController::class, 'edit'])->name('movies.update');
Route::delete('/movies/{movie}', [MovieController::class, 'destroy'])->name('movies.destroy');
