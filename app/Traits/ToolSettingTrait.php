<?php

namespace App\Traits;

use App\Helpers\HoomdossierSession;
use App\Services\ToolSettingService;
use Illuminate\Database\Eloquent\Model;

trait ToolSettingTrait {

    public static function bootToolSettingTrait()
    {
        static::created(function (Model $model) {
            ToolSettingService::setChanged(HoomdossierSession::getBuilding(), HoomdossierSession::getInputSource(),true);
        });

        static::updated(function (Model $model) {
            ToolSettingService::setChanged(HoomdossierSession::getBuilding(), HoomdossierSession::getInputSource(), true);
        });
    }
}