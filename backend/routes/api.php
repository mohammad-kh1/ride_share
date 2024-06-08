<?php

use App\Http\Controllers\DriverController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\TripController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [LoginController::class, 'submit']);
Route::post('/login/verify', [LoginController::class, 'verifiy']);


Route::group(['middleware' => 'auth:sanctum'], function () {

    Route::get('/driver' , [DriverController::class , 'show']);
    Route::post('/driver' , [DriverController::class , 'update']);

    Route::post('/trip' , [TripController::class , 'store']);
    Route::get('/trip/{trip}' , [TripController::class , 'show']);
    Route::get('/trip/{trip}/accept' , [TripController::class , 'accept']);
    Route::get('/trip/{trip}/start' , [TripController::class , 'start']);
    Route::get('/trip/{trip}/end' , [TripController::class , 'end']);
    Route::get('/trip/{trip}/location' , [TripController::class , 'location']);


    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
