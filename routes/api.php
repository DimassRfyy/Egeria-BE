<?php

use App\Http\Controllers\Api\BookingTransactionController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CosmeticController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/cosmetic/{cosmetic:slug}',[CosmeticController::class,'show']);
Route::apiResource('/cosmetics', CosmeticController::class);

Route::get('/category/{category:slug}',[CategoryController::class,'show']);
Route::apiResource('/categories', CategoryController::class);

Route::get('/brand/{brand:slug}',[BrandController::class,'show']);
Route::apiResource('/brands', BrandController::class);

Route::post('/booking-transaction', [BookingTransactionController::class, 'store']);
Route::post('/check-booking', [BookingTransactionController::class,'booking_details']);
