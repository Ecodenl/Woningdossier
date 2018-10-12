<?php

namespace App\Models;

use App\Scopes\GetValueScope;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Building.
 *
 * @property int $id
 * @property int $user_id
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
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingElement[] $buildingElements
 * @property \App\Models\BuildingFeature $buildingFeatures
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingService[] $buildingServices
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingInsulatedGlazing[] $currentInsulatedGlazing
 * @property \App\Models\BuildingPaintworkStatus $currentPaintworkStatus
 * @property \App\Models\ExampleBuilding|null $exampleBuilding
 * @property \App\Models\BuildingHeater $heater
 * @property \App\Models\BuildingPvPanel $pvPanels
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingRoofType[] $roofTypes
 * @property \App\Models\User $user
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingUserUsage[] $userUsage
 *
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
 * @mixin \Eloquent
 */
class Building extends Model
{
    public $fillable = [
        'street', 'number', 'city', 'postal_code', 'bag_addressid', 'building_coach_status_id',
    ];

    public function buildingNotes()
    {
        return $this->hasMany('App\Models\BuildingNotes');
	}
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userUsage()
    {
        return $this->hasMany(BuildingUserUsage::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function buildingFeatures()
    {
        return $this->hasOne(BuildingFeature::class);
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
        $buildingService = $this->getBuildingService($short);
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
}
