<?php

use App\Http\Controllers\Api;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\BuildingCoachStatusController;
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

// V1 API
Route::domain('{cooperation}.' . config('hoomdossier.domain'))
    ->middleware(['auth:sanctum', 'cooperation', 'access.cooperation'])
    ->as('v1.cooperation.')
    ->prefix('v1')
    ->group(function () {
        Route::get('', [Controller::class, 'index'])->name('index');
        Route::post('register', [RegisterController::class, 'store'])->name('register.store');
        Route::post('building-coach-status', [BuildingCoachStatusController::class, 'buildingCoachStatus'])
            ->name('building-coach-status.store');
    });

// Non-cooperation internal API
Route::get('address-data', [Api\GeoController::class, 'getAddressData'])->name('get-address-data');
// Not cooperation route because it shares with the super admin in which a cooperation domain doesn't work.
Route::get('check-address-duplicates/{cooperation}', [Api\AddressController::class, 'checkDuplicates'])
    ->name('check-address-duplicates');