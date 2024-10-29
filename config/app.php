<?php

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Route;

return [

    'version' => env('APP_VERSION', '1.2.0'),

    'domain' => env('APP_DOMAIN', 'hoomdossier.nl'),

    'providers' => ServiceProvider::defaultProviders()->merge([
//        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        App\Providers\PasswordResetServiceProvider::class,

        /*
         * Package Service Providers...
         */
        App\Providers\FortifyServiceProvider::class,

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,

        App\Providers\ExternalServiceProvider::class,
        App\Providers\EconobisServiceProvider::class,
        App\Providers\WoningdossierServiceProvider::class,
        App\Providers\MacroServiceProvider::class,
        App\Providers\ViewServiceProvider::class,
    ])->toArray(),

    'aliases' => Facade::defaultAliases()->merge([
        'Redis' => Illuminate\Support\Facades\Redis::class,
        'Caster' => App\Helpers\DataTypes\Caster::class,
        'CooperationSettingHelper' => App\Helpers\Models\CooperationSettingHelper::class,
        'Hoomdossier' => App\Helpers\Hoomdossier::class,
        'HoomdossierSession' => App\Helpers\HoomdossierSession::class,
        'Kengetallen' => App\Helpers\Kengetallen::class,
        'MediaHelper' => App\Helpers\MediaHelper::class,
        'RoleHelper' => App\Helpers\RoleHelper::class,
        'RouteLogic' => App\Helpers\Blade\RouteLogic::class,
    ])->toArray(),

];
