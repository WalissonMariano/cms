<?php

use App\Http\Controllers\Api\Posts\CategoryController;
use App\Http\Controllers\Api\Posts\PublishedPostController;
use App\Http\Controllers\Api\Posts\TagController;
use Illuminate\Support\Facades\Route;

Route::get('/posts', [PublishedPostController::class, 'index']);
Route::get('/posts/slug/{slug}', [PublishedPostController::class, 'showBySlug']);
Route::get('/posts/{post}', [PublishedPostController::class, 'show'])->whereNumber('post');
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/tags', [TagController::class, 'index']);
