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

Route::middleware(['api/v1', CacheJsonResponse::class])->group(function () {
    Route::get('/employee/{name}', [App\Http\Controllers\EmployeeController::class, 'getEmployersByName']);
});

