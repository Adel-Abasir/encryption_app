<?php

namespace App\Http\Controllers\FileController;

use App\Http\Controllers\FileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome', ['file_selected' => false]);
});

Route::post('/file_processing',  [FileController::class, 'file_processing'])->name('file.processing');
Route::post('/file_download',  [FileController::class, 'file_download'])->name('file.download');