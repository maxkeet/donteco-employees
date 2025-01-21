<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CacheJsonResponse;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')
    ->middleware(['api', CacheJsonResponse::class])
    ->group(function () {
    Route::get('/employee', [App\Http\Controllers\EmployeeController::class, 'searchByName']);
    Route::get('/employee/{name}', [App\Http\Controllers\EmployeeController::class, 'getByFullName']);
});

