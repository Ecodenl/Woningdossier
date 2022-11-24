<?php

use App\Http\Controllers\Api;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\RegisterController;
use App\Http\Controllers\Api\V1\Controller;

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

Route::domain('{cooperation}.' . config('hoomdossier.domain'))
    ->middleware(['auth:sanctum', 'cooperation', 'access.cooperation'])
    ->as('v1.cooperation.')
    ->prefix('v1')
    ->group(function () {
        Route::get('', [Controller::class, 'index'])->name('index');
        Route::post('register', [RegisterController::class, 'store'])->name('register.store');
    });

Route::get('address-data', [Api\GeoController::class, 'getAddressData'])->name('get-address-data');

