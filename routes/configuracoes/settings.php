<?php

use App\Http\Controllers\Configuracoes\SettingController;
use Illuminate\Support\Facades\Route;

Route::middleware('admin')->prefix('configuracoes')->name('configuracoes.')->group(function () {
    Route::get('/', [SettingController::class, 'edit'])->name('edit');
    Route::put('/', [SettingController::class, 'update'])->name('update');
});
