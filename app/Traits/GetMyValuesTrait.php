<?php

namespace App\Traits;

use App\Helpers\HoomdossierSession;
use App\Models\Building;
use App\Models\CooperationMeasureApplication;
use App\Models\CustomMeasureApplication;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\User;
use App\Models\UserActionPlanAdvice;
use App\Scopes\GetValueScope;
use App\Scopes\VisibleScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

trait GetMyValuesTrait
{

    /**
     * boot this trait
     * (https://www.archybold.com/blog/post/booting-eloquent-model-traits)
     */
    public static function bootGetMyValuesTrait()
    {
        static::saved(function (Model $model) {
            // might be handy to prevent getting into an infinite loop (-:>
            if (($model->inputSource->short ?? '') !== InputSource::MASTER_SHORT) {
                $model->saveForMasterInputSource();
            }
        });
    }

    /**
     * Returns if this model has a particular attribute.
     * This should be done on the database table as this is also used during
     * creation in which attributes may not be set.
     *
     * @param  string  $attribute
     *
     * @return bool
     */
    public function hasAttribute(string $attribute): bool
    {
        return (Schema::hasColumn($this->getTable(), $attribute));
    }

    protected function saveForMasterInputSource()
    {
        $masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
        $data = $this->attributesToArray();

        $data['input_source_id'] = $masterInputSource->id;
        unset($data['id']);

        $wheres = [
            'input_source_id' => $masterInputSource->id,
        ];

        $crucialRelationCombinationIds = [
            'user_id', 'building_id', 'tool_question_id', 'element_id', 'service_id',
            'hash',
        ];
        if ($this instanceof UserActionPlanAdvice) {
            $advisable = $this->userActionPlanAdvisable;
            if ($advisable instanceof MeasureApplication || $advisable instanceof CooperationMeasureApplication) {
                $crucialRelationCombinationIds[] = 'user_action_plan_advisable_id';
                $crucialRelationCombinationIds[] = 'user_action_plan_advisable_type';
            } elseif ($advisable instanceof CustomMeasureApplication) {
                // find sibling of the user one with admin input source
                $sibling = $advisable->getSibling($masterInputSource);
                $data['user_action_plan_advisable_id'] = $sibling->id;
                $wheres['user_action_plan_advisable_id'] = $sibling->id;
                $crucialRelationCombinationIds[] = 'user_action_plan_advisable_type';
            }
        }

        foreach ($crucialRelationCombinationIds as $crucialRelationCombinationId) {
            if ($this->hasAttribute($crucialRelationCombinationId)) {
                $wheres[$crucialRelationCombinationId] = $this->getAttributeValue($crucialRelationCombinationId);
            }
        }

        ($this)::withoutGlobalScope(VisibleScope::class)
            ->allInputSources()
            ->updateOrCreate(
                $wheres,
                $data,
            );
    }

    /**
     * Scope all the available input for a user.
     *
     * @param $query
     *
     * @return mixed
     */
    public function scopeForMe($query, User $user = null)
    {
        $whereUserOrBuildingId = $this->determineWhereColumn($user);

        return $query->withoutGlobalScope(GetValueScope::class)
            ->where($whereUserOrBuildingId)
            ->join('input_sources',
                $this->getTable() . '.input_source_id', '=',
                'input_sources.id')
            ->orderBy('input_sources.order', 'ASC')
            ->select([$this->getTable() . '.*']);
    }

    public function scopeForBuilding(Builder $query, Building $building)
    {
        return $query->where('building_id', $building->id);
    }

    public function scopeAllInputSources(Builder $query)
    {
        return $query->withoutGlobalScope(GetValueScope::class);
    }

    /**
     * Get the input source.
     *
     * @return BelongsTo
     */
    public function inputSource()
    {
        return $this->belongsTo(InputSource::class);
    }

    /**
     * Scope a query for a specific input source id.
     *
     * @return Builder
     */
    public function scopeForInputSource(
        Builder $query,
        InputSource $inputSource
    )
    {
        return $query->withoutGlobalScope(GetValueScope::class)->where('input_source_id',
            $inputSource->id);
    }

    /**
     * Determine if we should query on the user or building id.
     */
    protected function determineWhereColumn(User $user = null): array
    {
        // because recent changes in the application with jobs / commands running on the commandline we need to obtain data from objects as much as possible
        // so for now, if the user is given we will get the building from that and otherwise from the session. In the future we should get rid of session usage in methods as much as we can.
        $building = $user->building ?? HoomdossierSession::getBuilding(true);

        // determine what table we are using
        $currentTable = $this->table ?? $this->getTable();

        // determine which column we should use.
        if (\Schema::hasColumn($currentTable, 'building_id')) {
            return [['building_id', '=', $building->id]];
        } else {
            return [['user_id', '=', $building->user_id]];
        }
    }

    /**
     * Method to only scope the resident input source.
     *
     * @param $query
     *
     * @return mixed
     * @deprecated
     *
     */
    public function scopeResidentInput($query)
    {
        $residentInputSource = InputSource::findByShort('resident');

        return $query->where('input_source_id', $residentInputSource->id);
    }

    /**
     * Check on a collection that comes from the forMe() scope if it contains a
     * Coach input source.
     */
    public static function hasCoachInputSource(Collection $inputSourcesForMe): bool
    {
        $coachInputSource = InputSource::findByShort('coach');
        if ($inputSourcesForMe->contains('input_source_id', $coachInputSource->id)) {
            return true;
        }

        return false;
    }

    /**
     * Check on a collection that comes from the forMe() scope if it contains a
     * resident input source.
     *tom.
     */
    public static function hasResidentInputSource(Collection $inputSourcesForMe): bool
    {
        $residentInputSource = InputSource::findByShort('resident');
        if ($inputSourcesForMe->contains('input_source_id', $residentInputSource->id)) {
            return true;
        }

        return false;
    }

    /**
     * Get the coach input from a collection that comes from the forMe() scope.
     *
     * @return mixed
     */
    public static function getCoachInput(Collection $inputSourcesForMe)
    {
        $coachInputSource = InputSource::findByShort('coach');
        if (self::hasCoachInputSource($inputSourcesForMe)) {
            return $inputSourcesForMe->where('input_source_id',
                $coachInputSource->id)->first();
        }
    }

    /**
     * Get the resident input from a collection that comes from the forMe() scope.
     *
     * @return mixed
     */
    public static function getResidentInput(Collection $inputSourcesForMe)
    {
        $residentInputSource = InputSource::findByShort('resident');

        if (self::hasResidentInputSource($inputSourcesForMe)) {
            return $inputSourcesForMe->where('input_source_id',
                $residentInputSource->id)->first();
        }
    }

    /**
     * Get a input source name.
     *
     * @return string name
     */
    public function getInputSourceName()
    {
        return $this->inputSource->name;
    }
}
