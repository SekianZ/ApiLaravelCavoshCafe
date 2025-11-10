<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/** USUARIOS **/
Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

/** CATEGORIAS **/
Route::middleware('auth:sanctum')->prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::post('/', [CategoryController::class, 'store']);
    Route::get('/{category}', [CategoryController::class, 'show']);
    Route::match(['put', 'patch'], '/{category}', [CategoryController::class, 'update']);
    Route::delete('/{category}', [CategoryController::class, 'destroy']);
});

/** PRODUCTOS **/
Route::middleware('auth:sanctum')->prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);

    Route::post('/', [ProductController::class, 'store']);

    Route::get('/{product}', [ProductController::class, 'show']);

    Route::match(['put', 'patch'], '/{product}', [ProductController::class, 'update']);

    Route::delete('/{product}', [ProductController::class, 'destroy']);

    // ========== GESTIÓN DE IMÁGENES ==========

    Route::post('/{product}/image', [ProductController::class, 'uploadImage']);

    Route::delete('/{product}/image', [ProductController::class, 'deleteImage']);
});
