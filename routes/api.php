<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('products', [App\Http\Controllers\API\ProductController::class, 'all']);
Route::get('categories', [App\Http\Controllers\API\ProductCategoryController::class, 'all']);

Route::post('register', [App\Http\Controllers\API\UserController::class, 'register']);
