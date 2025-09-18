<?php

use App\Http\Controllers\Api\Frontend\ProductFilterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');



Route::get('categories', [ProductFilterController::class, 'categories']);
Route::get('categories/{id}/subcategories', [ProductFilterController::class, 'subcategories']);
Route::get('products/filter', [ProductFilterController::class, 'filter']); // returns JSON for frontend
