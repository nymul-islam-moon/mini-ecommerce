<?php

use App\Http\Controllers\Api\Frontend\ProductFilterController;
use App\Http\Controllers\Api\SelectController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');



Route::get('categories', [SelectController::class, 'categories'])->name('api.categories');
Route::get('categories/{id}/subcategories', [SelectController::class, 'subcategories'])->name('api.sub-categories');
Route::get('products/filter', [ProductFilterController::class, 'filter']); // returns JSON for frontend
