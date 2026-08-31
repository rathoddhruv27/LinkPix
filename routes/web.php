<?php

use App\Http\Controllers\ImageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ImageController::class, 'index'])->name('images.index');
Route::post('/upload', [ImageController::class, 'store'])->name('images.upload');
Route::get('/image/{key}', [ImageController::class, 'show'])->name('images.show');
