<?php

use Illuminate\Support\Facades\Facade;

return [
    'version' => env('APP_VERSION', '1.2.0'),

    'domain' => env('APP_DOMAIN', 'hoomdossier.nl'),

    'timezone' => 'Europe/Amsterdam',

    'aliases' => Facade::defaultAliases()->merge([
        'Str' => \App\Helpers\Str::class,
        'Arr' => \App\Helpers\Arr::class,
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
