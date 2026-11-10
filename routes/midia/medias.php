<?php

use App\Http\Controllers\Midia\MediaController;
use Illuminate\Support\Facades\Route;

Route::middleware('admin')->prefix('midia')->name('midia.')->group(function () {
    Route::get('/', [MediaController::class, 'index'])->name('index');
    Route::post('/', [MediaController::class, 'store'])->name('store');
    Route::post('/{media}/resize', [MediaController::class, 'resize'])->name('resize');
    Route::delete('/{media}', [MediaController::class, 'destroy'])->name('destroy');
});
