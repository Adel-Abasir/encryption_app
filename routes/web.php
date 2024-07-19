<?php

namespace App\Http\Controllers\FileController;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\FileController;
use App\Http\Controllers\LargeFileController;
use App\Http\Controllers\FileUploadController;

Route::get('/', function () {
    return view('welcome');
});

// Route::post('/file_processing',  [FileController::class, 'file_processing'])->name('file.processing');
// Route::post('/file_download',  [FileController::class, 'file_download'])->name('file.download');

Route::post('/file_processing',  [LargeFileController::class, 'file_processing'])->name('file.processing');
Route::post('/file_download',  [LargeFileController::class, 'file_download'])->name('file.download');

Route::post('/upload',  [FileUploadController::class, 'upload'])->name('upload');