<?php

use App\Http\Controllers\Api\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/** USUARIOS **/
Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});


/** CATEGORIAS **/
Route::middleware('auth:sanctum')->prefix('categories')->group(function () {
    Route::post('/',[CategoryController::class, 'store']);
    Route::get('/',[CategoryController::class, 'index']);
    Route::match(['put','patch'],'/{category}',[CategoryController::class, 'update']);
    Route::delete('/{category}',[CategoryController::class,'destroy']);
});
