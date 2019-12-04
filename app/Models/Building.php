<?php

namespace App\Models;

use App\Helpers\HoomdossierSession;
use App\Scopes\GetValueScope;
use App\Traits\ToolSettingTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Input;

/**
 * App\Models\Building.
 *
 * @property int                                                                             $id
 * @property int|null                                                                        $user_id
 * @property string                                                                          $street
 * @property string                                                                          $number
 * @property string                                                                          $extension
 * @property string                                                                          $city
 * @property string                                                                          $postal_code
 * @property string                                                                          $country_code
 * @property int|null                                                                        $owner
 * @property int                                                                             $primary
 * @property string                                                                          $bag_addressid
 * @property int|null                                                                        $example_building_id
 * @property \Illuminate\Support\Carbon|null                                                 $created_at
 * @property \Illuminate\Support\Carbon|null                                                 $updated_at
 * @property \Illuminate\Support\Carbon|null                                                 $deleted_at
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingCoachStatus[]      $buildingCoachStatuses
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingElement[]          $buildingElements
 * @property \App\Models\BuildingFeature                                                     $buildingFeatures
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingNotes[]            $buildingNotes
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingPermission[]       $buildingPermissions
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingService[]          $buildingServices
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingStatus[]           $buildingStatuses
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\CompletedStep[]             $completedSteps
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingInsulatedGlazing[] $currentInsulatedGlazing
 * @property \App\Models\BuildingPaintworkStatus                                             $currentPaintworkStatus
 * @property \App\Models\ExampleBuilding|null                                                $exampleBuilding
 * @property \App\Models\BuildingHeater                                                      $heater
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\PrivateMessage[]           $privateMessages
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\CompletedStep[]             $progress
 * @property \App\Models\BuildingPvPanel                                                     $pvPanels
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\QuestionsAnswer[]          $questionAnswers
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingRoofType[]         $roofTypes
 * @property \App\Models\User|null                                                           $user
 *
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building whereStreet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Building whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Building withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Building withoutTrashed()
 * @mixin \Eloquent
 */
class Building extends Model
{
    use SoftDeletes;
    use ToolSettingTrait;

    protected $dates = [
        'deleted_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public $fillable = [
        'street', 'number', 'city', 'postal_code', 'bag_addressid', 'building_coach_status_id', 'extension', 'is_active',
    ];

    public static function toolSettingColumnsToCheck()
    {
        return ['example_building_id'];
    }

    public function stepComments()
    {
        return $this->hasMany(StepComment::class);
    }

    /**
     * Check if a step is completed for a building with matching input source id.
     *
     * @param Step $step
     * @return bool
     */
    public function hasCompleted(Step $step)
    {
        return $this->completedSteps()
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
        return $this->hasMany(CompletedStep::class);
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function exampleBuilding()
    {
        return $this->belongsTo(ExampleBuilding::class);
    }

    /**
     * @return HasMany
     */
    public function buildingVentilations()
    {
        return $this->hasMany(BuildingVentilation::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function progress()
    {
        return $this->hasMany(UserProgress::class);
    }

    /**
     * @return ExampleBuilding|null
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
    public function getBuildingElement($short, InputSource $inputSource)
    {
        return $this->buildingElements()
            ->forInputSource($inputSource)
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
    public function getBuildingService($short, InputSource $inputSource)
    {
        return $this->buildingServices()
            ->forInputSource($inputSource)
            ->leftJoin('services as s', 'building_services.service_id', '=', 's.id')
            ->where('s.short', $short)->first(['building_services.*']);
    }

    /**
     * @param string $short
     *
     * @return ServiceValue|null
     */
    public function getServiceValue($short, InputSource $inputSource)
    {
        /** @var BuildingService $buildingService */
        $buildingService = $this->getBuildingService($short, $inputSource);
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
     * Return the building type from a builing through the building features.
     *
     * @param InputSource $inputSource
     *
     * @return BuildingType|null
     */
    public function getBuildingType(InputSource $inputSource)
    {
        $buildingFeature = $this->buildingFeatures()->forInputSource($inputSource)->first();

        if ($buildingFeature instanceof BuildingFeature) {
            return $buildingFeature->buildingType;
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
     * Get all the statuses from a building.
     *
     * @return HasMany
     */
    public function buildingStatuses(): HasMany
    {
        return $this->hasMany(BuildingStatus::class);
    }

    /**
     * Get the most recent BuildingStatus.
     *
     * @return BuildingStatus|null
     */
    public function getMostRecentBuildingStatus()
    {
        return $this->buildingStatuses()->with('status')->mostRecent()->first();
    }

    private function resolveStatusModel($status)
    {
        $statusModel = null;

        if (is_string($status)) {
            $statusModel = Status::where('short', $status)->first();
        }

        if ($status instanceof Status) {
            $statusModel = $status;
        }

        return $statusModel;
    }

    /**
     * convenient way of setting a status on a building.
     *
     * @param string|Status $status
     *
     * @return void
     */
    public function setStatus($status)
    {
        $statusModel = $this->resolveStatusModel($status);

        $this->buildingStatuses()->create([
            'status_id' => $statusModel->id,
            'appointment_date' => optional($this->getMostRecentBuildingStatus())->appointment_date,
        ]);
    }

    /**
     * convenient way of setting a appointment date on a building.
     *
     * @param string
     *
     * @return void
     */
    public function setAppointmentDate($appointmentDate)
    {
        $this->buildingStatuses()->create([
            'status_id' => $this->getMostRecentBuildingStatus()->status_id,
            'appointment_date' => $appointmentDate,
        ]);
    }
}
