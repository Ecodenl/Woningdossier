<?php

namespace App\Models;

use App\Helpers\Calculator;
use App\Helpers\HoomdossierSession;
use App\Scopes\GetValueScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * App\Models\UserActionPlanAdvice.
 *
 * @property int $id
 * @property int $user_id
 * @property int $measure_application_id
 * @property float|null $costs
 * @property float|null $savings_gas
 * @property float|null $savings_electricity
 * @property float|null $savings_money
 * @property int|null $year
 * @property bool $planned
 * @property int|null $planned_year
 * @property int $step_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \App\Models\MeasureApplication $measureApplication
 * @property \App\Models\Step $step
 * @property \App\Models\User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice forMe()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice forStep(\App\Models\Step $step)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice whereCosts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice whereMeasureApplicationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice wherePlanned($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice wherePlannedYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice whereSavingsElectricity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice whereSavingsGas($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice whereSavingsMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice whereStepId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserActionPlanAdvice whereYear($value)
 * @mixin \Eloquent
 */
class UserActionPlanAdvice extends Model
{
    public $fillable = [
        'user_id', 'measure_application_id', // old
        'costs', 'savings_gas', 'savings_electricity', 'savings_money',
        'year', 'planned', 'planned_year',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'planned' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function measureApplication()
    {
        return $this->belongsTo(MeasureApplication::class);
    }

    public function step()
    {
        return $this->belongsTo(Step::class);
    }

    public static function getCategorizedActionPlan(User $user)
    {
        $result = [];
        $advices = self::where('user_id', $user->id)
                       ->orderBy('step_id', 'asc')
                       ->orderBy('year', 'asc')
                       ->get();
        /** @var UserActionPlanAdvice $advice */
        foreach ($advices as $advice) {
            /** @var MeasureApplication $measureApplication */
            $measureApplication = $advice->measureApplication;

            if (is_null($advice->year)) {
                $advice->year = $advice->getAdviceYear();
                // re-index costs
                $advice->costs = Calculator::reindexCosts($advice->costs, null, $advice->year);
            }

            if (! array_key_exists($measureApplication->measure_type, $result)) {
                $result[$measureApplication->measure_type] = [];
            }
            //			if (!array_key_exists($advice->step->slug, $result[$measureApplication->measure_type])) {
//                $result[$measureApplication->measure_type][$advice->step->slug] = [];
//            }

            if (! array_key_exists($advice->step->slug, $result[$measureApplication->measure_type])) {
                $result[$measureApplication->measure_type][$advice->step->slug] = [];
            }

            $result[$measureApplication->measure_type][$advice->step->slug][] = $advice;
        }

        ksort($result);
        return $result;
    }

    /**
     * Get all the comments that are saved in multiple tables
     *
     * @return Collection
     */
    public static function getAllCoachComments() : Collection
    {
        $building = Building::find(HoomdossierSession::getInputSource());
        $allInputForMe = collect();
        $coachComments = collect();
        $comment = "";

        /* General-data */
        $userEnergyHabitForMe = UserEnergyHabit::forMe()->get();
        $allInputForMe->put('general-data', $userEnergyHabitForMe);

        /* wall insulation */
        $buildingFeaturesForMe = BuildingFeature::forMe()->get();
        $allInputForMe->put('wall-insulation', $buildingFeaturesForMe);

        /* floor insualtion */
        $crawlspace = Element::where('short', 'crawlspace')->first();
        $buildingElementsForMe = BuildingElement::forMe()->get();
        $allInputForMe->put('floor-insulation', $buildingElementsForMe->where('element_id', $crawlspace->id));

        /* beglazing */
        $insulatedGlazingsForMe = $building->currentInsulatedGlazing()->forMe()->get();
        $allInputForMe->put('insulated-glazing', $insulatedGlazingsForMe);

        /* roof */
        $currentRoofTypesForMe = $building->roofTypes()->forMe()->get();
        $allInputForMe->put('roof-insulation', $currentRoofTypesForMe);

        /* hr boiler ketel */
        $boiler = Service::where('short', 'boiler')->first();
        $installedBoilerForMe = $building->buildingServices()->forMe()->where('service_id', $boiler->id)->get();
        $allInputForMe->put('high-efficiency-boiler', $installedBoilerForMe);


        foreach ($allInputForMe as $step => $inputForMe) {

            // get the coach his input from the collection
            $coachInputSource = InputSource::findByShort('coach');
            // get the coach answers
            $coachInputs = $inputForMe->where('input_source_id', $coachInputSource->id);

            // loop through them and extract the comments from them
            foreach ($coachInputs as $coachInput) {
                if (!is_null($coachInput)) {

                    if (is_array($coachInput->extra) && array_key_exists('comment', $coachInput->extra)) {
                        $comment = $coachInput->extra['comment'];
                    } elseif (array_key_exists('additional_info', $coachInput->attributes)) {
                        $comment = $coachInput->additional_info;
                    } elseif (array_key_exists('living_situation_extra', $coachInput->attributes)) {
                        $comment = $coachInput->living_situation_extra;
                    }

                    // for the rooftype there are multiple comments
                    if ($coachInput instanceof BuildingRoofType) {
                        $coachComments->put($step.'-'.str_slug(RoofType::find($coachInput->roof_type_id)->name), $comment);
                    } else {
                        // comment as key, yes. Comments will be unique.
                        $coachComments->put($step, $comment);

                    }
                }
            }


        }

        $coachComments = $coachComments->unique();

        return $coachComments;
    }

    public function getAdviceYear()
    {
        // todo Find a neater solution for this as this was one of many additions in hindsight
        // Step slug => element short
        $slugElements = [
            'wall-insulation' => 'wall-insulation',
            //'insulated-glazing' => 'living-rooms-windows', // this is nonsense.. there's no location specification in this step, while there is on general-data
            'floor-insulation' => 'floor-insulation',
            //'roof-insulation' => 'roof-insulation',
        ];
        if (! $this->step instanceof Step) {
            return null;
        }

        if ('insulated-glazing' == $this->step->slug) {
            $userInterest = $this->user->getInterestedType('measure_application', $this->measureApplication->id);
            if (! $userInterest instanceof UserInterest) {
                return null;
            }
            if (1 == $userInterest->interest->calculate_value) {
                return Carbon::now()->year;
            }
            if (2 == $userInterest->interest->calculate_value) {
                return Carbon::now()->year + 5;
            }

            return null;
        }

        if (! array_key_exists($this->step->slug, $slugElements)) {
            return null;
        }
        $elementShort = $slugElements[$this->step->slug];
        $element = Element::where('short', $elementShort)->first();
        if (! $element instanceof Element) {
            return null;
        }
        $userInterest = $this->user->getInterestedType('element', $element->id);
        if (! $userInterest instanceof UserInterest) {
            return null;
        }
        if (1 == $userInterest->interest->calculate_value) {
            return Carbon::now()->year;
        }
        if (2 == $userInterest->interest->calculate_value) {
            return Carbon::now()->year + 5;
        }

        return null;
    }

    /**
     * Scope a query to only include the current user.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForMe($query)
    {
        return $query->where('user_id', Building::find(HoomdossierSession::getBuilding())->user_id);
    }

    /**
     * Scope a query to only include results for the particular step.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param Step                                  $step
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForStep($query, Step $step)
    {
        return $query->where('step_id', $step->id);
    }
}
