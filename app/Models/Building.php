<?php

namespace App\Models;

use App\Helpers\HoomdossierSession;
use App\Scopes\GetValueScope;
use App\Traits\ToolSettingTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Building
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $status
 * @property string $street
 * @property string $number
 * @property string $extension
 * @property string $city
 * @property string $postal_code
 * @property string $country_code
 * @property int|null $owner
 * @property int $primary
 * @property string $bag_addressid
 * @property int|null $example_building_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingCoachStatus[] $buildingCoachStatuses
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingElement[] $buildingElements
 * @property-read \App\Models\BuildingFeature $buildingFeatures
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingNotes[] $buildingNotes
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingPermission[] $buildingPermissions
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingService[] $buildingServices
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserProgress[] $completedSteps
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingInsulatedGlazing[] $currentInsulatedGlazing
 * @property-read \App\Models\BuildingPaintworkStatus $currentPaintworkStatus
 * @property-read \App\Models\ExampleBuilding|null $exampleBuilding
 * @property-read \App\Models\BuildingHeater $heater
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PrivateMessage[] $privateMessages
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserProgress[] $progress
 * @property-read \App\Models\BuildingPvPanel $pvPanels
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\QuestionsAnswer[] $questionAnswers
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingRoofType[] $roofTypes
 * @property-read \App\Models\User|null $user
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Building onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building whereBagAddressid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building whereCountryCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building whereExampleBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building whereExtension($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building whereOwner($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building wherePrimary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building whereStreet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Building withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Building withoutTrashed()
 * @mixin \Eloquent
 */
class Building extends Model
{
    use SoftDeletes, ToolSettingTrait;

    const STATUS_IS_ACTIVE = 'active';
    const STATUS_IS_NOT_ACTIVE = 'in_active';

    protected $dates = [
        'deleted_at',
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public $fillable = [
        'street', 'number', 'city', 'postal_code', 'bag_addressid', 'building_coach_status_id', 'extension', 'is_active'
    ];

    public static function toolSettingColumnsToCheck()
    {
        return ['example_building_id'];
    }

    /**
     * Check if a step is completed for a building with matching input source id.
     *
     * @param Step $step
     *
     * @return bool
     */
    public function hasCompleted(Step $step)
    {
        return $this->find(HoomdossierSession::getBuilding())
                ->completedSteps()
                ->where('step_id', $step->id)->count() > 0;
    }

    /**
     * Check if a step is not completed.
     *
     * @param Step $step
     *
     * @return bool
     */
    public function hasNotCompleted(Step $step)
    {
        return ! $this->hasCompleted($step);
    }

    /**
     * Returns the user progress.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function completedSteps()
    {
        return $this->hasMany(UserProgress::class);
    }

    /**
     * Check if a user is interested in a step.
     *
     * @param string $type
     * @param array  $interestedInIds
     *
     * @return bool
     */
    public function isInterestedInStep($type, $interestedInIds = [])
    {
        // the interest ids that people select when they do not have any interest
        $noInterestIds = [4, 5];

        $interestedIds = [];

        if (! is_array($interestedInIds)) {
            $interestedInIds = [$interestedInIds];
        }

        // go through the elementid and get the user interest id to put them into the array
        foreach ($interestedInIds as $key => $interestedInId) {
            if ($this->user->getInterestedType($type, $interestedInId) instanceof UserInterest) {
                array_push($interestedIds, $this->user->getInterestedType($type, $interestedInId)->interest_id);
            }
        }

        // check if the user wants to do something with their glazing
        if ($interestedIds == array_intersect($interestedIds, $noInterestIds) && $this->user->getInterestedType($type, $interestedInId) instanceof UserInterest) {
            return false;
        }

        return true;
    }

    /**
     * Check if a user is not interested in a step.
     *
     * @param string $type
     * @param array  $interestedInIds
     *
     * @return bool
     */
    public function isNotInterestedInStep($type, $interestedInIds = [])
    {
        return ! $this->isInterestedInStep($type, $interestedInIds);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function buildingFeatures()
    {
        return $this->hasOne(BuildingFeature::class);
    }

    /**
     * Return all the building notes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function buildingNotes()
    {
        return $this->hasMany(BuildingNotes::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function buildingElements()
    {
        return $this->hasMany(BuildingElement::class);
    }

    public function exampleBuilding()
    {
        return $this->belongsTo(ExampleBuilding::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function progress()
    {
        return $this->hasMany(UserProgress::class);
    }

    /**
     * @return null|ExampleBuilding
     */
    public function getExampleBuilding()
    {
        $example = $this->exampleBuilding;
        if ($example instanceof ExampleBuilding) {
            return $example;
        }

        return $this->getFittingExampleBuilding();
    }

    /**
     * @return ExampleBuilding|null
     */
    public function getFittingExampleBuilding()
    {
        // determine fitting example building based on year + house type
        $features = $this->buildingFeatures;
        if (! $features instanceof BuildingFeature) {
            return null;
        }
        if (! $features->buildingType instanceof BuildingType) {
            return null;
        }
        $example = ExampleBuilding::whereNull('cooperation_id')
                       ->where('buiding_type_id', $features->buildingType->id)
                        ->first();

        return $example;
    }

    public function getExampleValueForStep(Step $step, $formKey)
    {
        return $this->getExampleValue($step->slug.'.'.$formKey);
    }

    public function getExampleValue($key)
    {
        $example = $this->getExampleBuilding();
        if (! $example instanceof ExampleBuilding) {
            return null;
        }

        return $example->getExampleValueForYear($this->getBuildYear(), $key);
    }

    public function getBuildYear()
    {
        if (! $this->buildingFeatures instanceof BuildingFeature) {
            return null;
        }

        return $this->buildingFeatures->build_year;
    }

    /**
     * @param $short
     *
     * @return BuildingElement|null
     */
    public function getBuildingElement($short)
    {
        return $this->buildingElements()
            ->leftJoin('elements as e', 'building_elements.element_id', '=', 'e.id')
            ->where('e.short', $short)->first(['building_elements.*']);
    }

    /**
     * Almost the same as getBuildingElement($short) except this returns all the input.
     *
     * @param $query
     * @param $short
     *
     * @return mixed
     */
    public function getBuildingElementsForMe($short)
    {
        return $this->buildingElements()
            ->withoutGlobalScope(GetValueScope::class)
            ->leftJoin('elements as e', 'building_elements.element_id', '=', 'e.id')
            ->where('e.short', $short)->select(['building_elements.*'])->get();
    }

    /**
     * @param string $short
     *
     * @return BuildingService|null
     */
    public function getBuildingService($short)
    {
        return $this->buildingServices()
            ->leftJoin('services as s', 'building_services.service_id', '=', 's.id')
            ->where('s.short', $short)->first(['building_services.*']);
    }

    /**
     * @param string $short
     *
     * @return ServiceValue|null
     */
    public function getServiceValue($short)
    {
        /** @var BuildingService $buildingService */
        $buildingService = $this->getBuildingService($short); dd($this->buildingServices);
        $serviceValue = $buildingService->serviceValue;

        return $serviceValue;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function buildingServices()
    {
        return $this->hasMany(BuildingService::class);
    }

    /**
     * @return BuildingType|null
     */
    public function getBuildingType()
    {
        if ($this->buildingFeatures instanceof BuildingFeature) {
            return $this->buildingFeatures->buildingType;
        }

        return null;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function currentInsulatedGlazing()
    {
        return $this->hasMany(BuildingInsulatedGlazing::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function currentPaintworkStatus()
    {
        return $this->hasOne(BuildingPaintworkStatus::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function pvPanels()
    {
        return $this->hasOne(BuildingPvPanel::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function heater()
    {
        return $this->hasOne(BuildingHeater::class);
    }

    /**
     * Returns all roof types of this building. Get the primary via the
     * building features.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function roofTypes()
    {
        return $this->hasMany(BuildingRoofType::class);
    }

    /**
     * Get all the statuses for a building.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function buildingCoachStatuses()
    {
        return $this->hasMany(BuildingCoachStatus::class);
    }

    public function buildingPermissions()
    {
        return $this->hasMany(BuildingPermission::class);
    }

    /**
     * Get all the answers for the building.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function questionAnswers()
    {
        return $this->hasMany(QuestionsAnswer::class);
    }

    /**
     * Get the private messages for a building.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function privateMessages(): HasMany
    {
        return $this->hasMany(PrivateMessage::class);
    }

    /**
     * Check if a building is active
     *
     * @return bool
     */
    public function isActive(): bool
    {
        if ($this->status == static::STATUS_IS_ACTIVE) {
            return true;
        }
        return false;
    }

    public function isNotActive()
    {
        return !$this->isActive();
    }

    /**
     * Return the translation from a status
     *
     * @param $status
     * @return string
     */
    public static function getTranslationForStatus($status): string
    {
        return __('woningdossier.building-statuses.'.$status);
    }

}
