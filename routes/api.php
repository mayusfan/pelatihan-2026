<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
// Route untuk register
Route::post('/register', [AuthController::class, 'register']);

// Route privat (butuh Header: Authorization: Bearer <token>)
Route::middleware('auth:sanctum')->group(function () {

    // Route untuk mendapatkan data user yang sedang login
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Route untuk mendapatkan data GeoJSON kota Yogyakarta
    Route::get('/geojson-data', [ApiController::class, 'geojson_data'])->name('api.geojson.data');


    // Route untuk logout
    Route::post('/logout', [AuthController::class, 'logout']);
});
