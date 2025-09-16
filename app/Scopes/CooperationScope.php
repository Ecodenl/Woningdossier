<?php

namespace App\Scopes;

use App\Helpers\HoomdossierSession;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class CooperationScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // the getCooperation method should always return an id, however if the app is running in the cli there is no session to retrieve.
        // so we set the id to 0 and depend on our own to add the cooperation_id = id query our self.
        $cooperationId = HoomdossierSession::getCooperation() ?? 0;

        $builder->where('cooperation_id', '=', $cooperationId)
            ->orWhere('cooperation_id', '=', null);
    }
}
