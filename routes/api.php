<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::get('/geojson-data', [ApiController::class, 'geojson_data'])->name('api.geojson.data')->middleware('auth:sanctum');
Route::post('/login', [AuthController::class, 'login']);
