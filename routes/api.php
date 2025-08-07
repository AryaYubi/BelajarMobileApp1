<?php

use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');








Route::get('products', [App\Http\Controllers\API\ProductController::class, 'all']);
Route::get('categories', [App\Http\Controllers\API\ProductCategoryController::class, 'all']);

Route::post('register', [App\Http\Controllers\API\UserController::class, 'register']);
Route::post('login', [App\Http\Controllers\API\UserController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', [UserController::class,'fetch']) ;
    Route::post('user', [UserController::class, 'updateProfile']);
    });
