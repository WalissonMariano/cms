<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'))->name('index');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {

    Route::get('/menu', function () {
        return view('layouts.menu');
    })->name('menu');

    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/account', [UserController::class, 'show'])->name('account.index');

    require base_path('routes/posts/categories.php');
    require base_path('routes/posts/tags.php');
    require base_path('routes/posts/posts.php');
    require base_path('routes/midia/medias.php');
    require base_path('routes/configuracoes/settings.php');

    Route::prefix('register')->name('register.')->group(function () {

        Route::get('/groups', [GroupController::class, 'index'])->name('groups.index');

        Route::get('/users', [UserController::class, 'index'])->name('users.index');

        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');

        Route::post('/users', [UserController::class, 'store'])->name('users.store');

        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');

        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');

        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        Route::get('/histories', [HistoryController::class, 'index'])->name('histories.index');
    });
});

Route::fallback(function () {
    return redirect()->route('index');
});
