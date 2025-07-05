<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CreneauxController;
Route::middleware('api')->group(function () {
    Route::get('/creneaux/{service_id}', [CreneauxController::class, 'getByService']);
});
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
