<?php

namespace App\Traits;

use App\Helpers\HoomdossierSession;
use App\Models\Building;
use App\Models\InputSource;
use App\Scopes\GetValueScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

trait GetMyValuesTrait
{

    /**
     * Scope all the available input for a user.
     *
     * @param $query
     *
     * @return mixed
     */
    public function scopeForMe($query)
    {
        $whereUserOrBuildingId = $this->determineWhereColumn();

        return $query->withoutGlobalScope(GetValueScope::class)
                     ->where($whereUserOrBuildingId)
                     ->join('input_sources', $this->getTable().'.input_source_id', '=', 'input_sources.id')
                     ->orderBy('input_sources.order', 'ASC')
                     ->select([$this->getTable().'.*']);
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
     * Scope a query for a specific input source id
     *
     * @param  Builder  $query
     * @param $inputSourceId
     *
     * @return Builder
     */
    public function scopeForInputSource(Builder $query, $inputSourceId)
    {
        return $query->withoutGlobalScopes()->where('input_source_id', $inputSourceId);
    }

    /**
     * Determine if we should query on the user or building id.
     *
     * @return array
     */
    protected function determineWhereColumn(): array
    {
        // determine what table we are using
        $currentTable = $this->table ?? $this->getTable();

        // determine which column we should use.
        if (\Schema::hasColumn($currentTable, 'building_id')) {
            return [['building_id', '=', HoomdossierSession::getBuilding()]];
        } else {
            $building = Building::find(HoomdossierSession::getBuilding());
            return [['user_id', '=', $building->user_id]];
        }
    }

    /**
     * Method to only scope the resident input source
     *
     * @param $query
     *
     * @return mixed
     */
    public function scopeResidentInput($query)
    {
        $residentInputSource = InputSource::findByShort('resident');

        return $query->where('input_source_id', $residentInputSource->id);
    }

    /**
     * Check on a collection that comes from the forMe() scope if it contains a
     * Coach input source.
     *
     * @param Collection $inputSourcesForMe
     *
     * @return bool
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
     *
     * @param Collection $inputSourcesForMe
     *
     * @return bool
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
     * @param Collection $inputSourcesForMe
     *
     * @return mixed
     */
    public static function getCoachInput(Collection $inputSourcesForMe)
    {
        $coachInputSource = InputSource::findByShort('coach');
        if (self::hasCoachInputSource($inputSourcesForMe)) {
            return $inputSourcesForMe->where('input_source_id', $coachInputSource->id)->first();
        }
    }

    /**
     * Get the resident input from a collection that comes from the forMe() scope.
     *
     * @param Collection $inputSourcesForMe
     *
     * @return mixed
     */
    public static function getResidentInput(Collection $inputSourcesForMe)
    {
        $residentInputSource = InputSource::findByShort('resident');

        if (self::hasResidentInputSource($inputSourcesForMe)) {
            return $inputSourcesForMe->where('input_source_id', $residentInputSource->id)->first();
        }
    }

    /**
     * Get a input source name.
     *
     * @return InputSource name
     */
    public function getInputSourceName()
    {
        return $this->inputSource()->first()->name;
    }
}
