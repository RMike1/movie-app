<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MovieStreamController;
use App\Http\Controllers\MovieUploadController;

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
    
    Route::get('movie/{slug}', [MovieUploadController::class, 'show'])->name('movie.show');
    Route::get('movies/{filename}',MovieStreamController::class)->name('video.stream');
    
    
});
Route::post('upload/upload-chunk', [MovieUploadController::class, 'uploadLargeFiles'])->name('files.upload.large');