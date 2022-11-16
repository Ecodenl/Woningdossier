<?php

namespace App\Models;

use App\Helpers\Arr;
use App\Helpers\QuestionValues\QuestionValue;
use App\Helpers\StepHelper;
use App\Helpers\ToolQuestionHelper;
use App\Scopes\GetValueScope;
use App\Traits\HasMedia;
use App\Traits\ToolSettingTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * App\Models\Building
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $street
 * @property string $number
 * @property string $extension
 * @property string $city
 * @property string $postal_code
 * @property string $country_code
 * @property int|null $owner
 * @property int $primary
 * @property string $bag_addressid
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingCoachStatus[] $buildingCoachStatuses
 * @property-read int|null $building_coach_statuses_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingElement[] $buildingElements
 * @property-read int|null $building_elements_count
 * @property-read \App\Models\BuildingFeature|null $buildingFeatures
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingNotes[] $buildingNotes
 * @property-read int|null $building_notes_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingPermission[] $buildingPermissions
 * @property-read int|null $building_permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingService[] $buildingServices
 * @property-read int|null $building_services_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingStatus[] $buildingStatuses
 * @property-read int|null $building_statuses_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingVentilation[] $buildingVentilations
 * @property-read int|null $building_ventilations_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CompletedStep[] $completedSteps
 * @property-read int|null $completed_steps_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CompletedSubStep[] $completedSubSteps
 * @property-read int|null $completed_sub_steps_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingInsulatedGlazing[] $currentInsulatedGlazing
 * @property-read int|null $current_insulated_glazing_count
 * @property-read \App\Models\BuildingPaintworkStatus|null $currentPaintworkStatus
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CustomMeasureApplication[] $customMeasureApplications
 * @property-read int|null $custom_measure_applications_count
 * @property-read \App\Models\ExampleBuilding $exampleBuilding
 * @property-read \App\Models\BuildingHeater|null $heater
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Media[] $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PrivateMessage[] $privateMessages
 * @property-read int|null $private_messages_count
 * @property-read \App\Models\BuildingPvPanel|null $pvPanels
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\QuestionsAnswer[] $questionAnswers
 * @property-read int|null $question_answers_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BuildingRoofType[] $roofTypes
 * @property-read int|null $roof_types_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\StepComment[] $stepComments
 * @property-read int|null $step_comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ToolQuestionAnswer[] $toolQuestionAnswers
 * @property-read int|null $tool_question_answers_count
 * @property-read \App\Models\User|null $user
 * @method static \Plank\Mediable\MediableCollection|static[] all($columns = ['*'])
 * @method static \Plank\Mediable\MediableCollection|static[] get($columns = ['*'])
 * @method static Builder|Building newModelQuery()
 * @method static Builder|Building newQuery()
 * @method static \Illuminate\Database\Query\Builder|Building onlyTrashed()
 * @method static Builder|Building query()
 * @method static Builder|Building whereBagAddressid($value)
 * @method static Builder|Building whereCity($value)
 * @method static Builder|Building whereCountryCode($value)
 * @method static Builder|Building whereCreatedAt($value)
 * @method static Builder|Building whereDeletedAt($value)
 * @method static Builder|Building whereExtension($value)
 * @method static Builder|Building whereHasMedia($tags = [], bool $matchAll = false)
 * @method static Builder|Building whereHasMediaMatchAll(array $tags)
 * @method static Builder|Building whereId($value)
 * @method static Builder|Building whereNumber($value)
 * @method static Builder|Building whereOwner($value)
 * @method static Builder|Building wherePostalCode($value)
 * @method static Builder|Building wherePrimary($value)
 * @method static Builder|Building whereStreet($value)
 * @method static Builder|Building whereUpdatedAt($value)
 * @method static Builder|Building whereUserId($value)
 * @method static Builder|Building withMedia($tags = [], bool $matchAll = false, bool $withVariants = false)
 * @method static Builder|Building withMediaAndVariants($tags = [], bool $matchAll = false)
 * @method static Builder|Building withMediaAndVariantsMatchAll($tags = [])
 * @method static Builder|Building withMediaMatchAll(bool $tags = [], bool $withVariants = false)
 * @method static Builder|Building withRecentBuildingStatusInformation()
 * @method static \Illuminate\Database\Query\Builder|Building withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Building withoutTrashed()
 * @mixin \Eloquent
 */
class Building extends Model
{
    use SoftDeletes,
        ToolSettingTrait,
        HasMedia;

    public $fillable = [
        'street',
        'number',
        'city',
        'postal_code',
        'bag_addressid',
        'building_coach_status_id',
        'extension',
        'is_active',
        'example_building_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function boot()
    {
        parent::boot();

        // The mediable only _detaches_ the media. We want to DELETE the media if set.
        static::deleting(function (Building $building) {
            $building->media()->delete();
        });
    }

    public function toolQuestionAnswers(): HasMany
    {
        return $this->hasMany(ToolQuestionAnswer::class);
    }

    public function customMeasureApplications(): HasMany
    {
        return $this->hasMany(CustomMeasureApplication::class);
    }

    public function getAnswerForAllInputSources(ToolQuestion $toolQuestion)
    {
        $inputSources = InputSource::all();

        $answers = null;
        $where   = [
            [
                'input_source_id',
                '!=',
                $inputSources->where('short', InputSource::MASTER_SHORT)->first()->id,
            ],
        ];
        // this means we should get the answer the "traditional way" , in another table (not from the tool_question_answers)
        if (! is_null($toolQuestion->save_in)) {
            $saveIn = ToolQuestionHelper::resolveSaveIn($toolQuestion, $this);
            $table  = $saveIn['table'];
            $column = $saveIn['column'];
            $where  = array_merge($saveIn['where'], $where);

            $modelName = "App\\Models\\" . Str::studly(Str::singular($table));

            // we do a get so we can make use of pluck on the collection, pluck can use dotted notation eg; extra.date
            $models = $modelName::allInputSources()
                                ->with('inputSource')
                                ->where($where)
                                ->get();

            // We pluck, as pluck handles dot notation for sub-values such as in JSON
            $values = $models->pluck($column, 'input_source_id');

            // We still loop though, to ensure we get human-readable answers
            foreach ($values as $inputSourceId => $value) {
                $inputSource = $inputSources->where('id', $inputSourceId)->first();

                // these contain the human-readable answers, we need this because the answer for a yes, no, unknown
                // could be a 1,2,3.
                $questionValues = QuestionValue::getQuestionValues($toolQuestion, $this, $inputSource)->pluck(
                    'name',
                    'value'
                );

                $answer = $questionValues->isNotEmpty() && ! is_null($value) && isset($questionValues[$value]) ? $questionValues[$value] : $value;

                $answers[$inputSource->short][] = [
                    'answer' => $answer,
                    'value'  => $value,
                ];
            }
        } else {
            $where['building_id'] = $this->id;
            $toolQuestionAnswers  = $toolQuestion
                ->toolQuestionAnswers()
                ->allInputSources()
                ->with('inputSource')
                ->where($where)
                ->get();
            foreach ($toolQuestionAnswers as $index => $toolQuestionAnswer) {
                $answer = optional($toolQuestionAnswer->toolQuestionCustomValue)->name ?? $toolQuestionAnswer->answer;
                $answers[$toolQuestionAnswer->inputSource->short][$index] = [
                    'answer' => $answer,
                    'value'  => $toolQuestionAnswer->toolQuestionCustomValue->short ?? null,
                ];
            }
        }

        // As last step, we want to clean up empty values
        foreach ($answers ?? [] as $short => $answer) {
            if (empty($answer) || (is_array($answer) && Arr::isWholeArrayEmpty($answer))) {
                unset($answers[$short]);
            }
        }

        return $answers;
    }

    /**
     * @param InputSource $inputSource
     * @param ToolQuestion $toolQuestion
     * @return array|mixed
     */
    public function getAnswer(InputSource $inputSource, ToolQuestion $toolQuestion)
    {
        // TODO: Should this check `for_specific_input_source`?

        $answer  = null;
        $where['input_source_id'] = $inputSource->id;
        // this means we should get the answer the "traditional way", in another table (not from the tool_question_answers)
        if (! is_null($toolQuestion->save_in)) {
            $saveIn = ToolQuestionHelper::resolveSaveIn($toolQuestion, $this);
            $table  = $saveIn['table'];
            $column = $saveIn['column'];
            $where  = array_merge($saveIn['where'], $where);

            $modelName = "App\\Models\\".Str::studly(Str::singular($table));

            // we do a get, so we can make use of pluck on the collection, pluck can use dotted notation eg; extra.date
            $answer = $modelName::allInputSources()->where($where)->get()->pluck($column)->first();
        } else {
            $where['building_id'] = $this->id;
            $toolQuestionAnswers  = $toolQuestion
                ->toolQuestionAnswers()
                ->allInputSources()
                ->where($where)
                ->get();

            // todo: refactor this to something sensible
            if ($toolQuestion->toolQuestionType->short == 'checkbox-icon') {
                foreach ($toolQuestionAnswers as $toolQuestionAnswer) {
                    if ($toolQuestionAnswer instanceof ToolQuestionAnswer) {
                        if ($toolQuestionAnswer->toolQuestionCustomValue instanceof ToolQuestionCustomValue) {
                            $answer[] = $toolQuestionAnswer->toolQuestionCustomValue->short;
                        } else {
                            $answer[] = $toolQuestionAnswer->answer;
                        }
                    }
                }
            } else {
                $toolQuestionAnswer = $toolQuestionAnswers->first();
                if ($toolQuestionAnswer instanceof ToolQuestionAnswer) {
                    $answer = $toolQuestionAnswer->answer;
                    if ($toolQuestionAnswer->toolQuestionCustomValue instanceof ToolQuestionCustomValue) {
                        $answer = $toolQuestionAnswer->toolQuestionCustomValue->short;
                    }
                }
            }
        }

        return $answer;
    }

    /**
     * Method to check whether a building is the owner of a file.
     */
    public function isOwnerOfFileStorage(
        InputSource $inputSource,
        FileStorage $fileStorage
    ): bool {
        $fileIsGeneratedByBuilding           = $fileStorage->building_id == $this->id;
        $fileInputSourceIsCurrentInputSource = $fileStorage->input_source_id == $inputSource->id;

        return $fileIsGeneratedByBuilding && $fileInputSourceIsCurrentInputSource;
    }

    public static function toolSettingColumnsToCheck()
    {
        return ['example_building_id'];
    }

    /**
     * Scope to return the buildings with most recent information from the building status.
     *
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeWithRecentBuildingStatusInformation(Builder $query
    ): Builder {
        $recentBuildingStatuses = DB::table('building_statuses')
                                    ->selectRaw(
                                        'building_id, max(created_at) as max_created_at, max(id) AS max_id'
                                    )
                                    ->groupByRaw('building_id');

        return $query->select([
            'buildings.*',
            'statuses.name as status_name_json',
            'appointment_date',
        ])->leftJoin(
            'building_statuses as bs',
            'bs.building_id',
            '=',
            'buildings.id'
        )
                     ->rightJoinSub(
                         $recentBuildingStatuses,
                         'bs2',
                         'bs2.max_id',
                         '=',
                         'bs.id'
                     )
                     ->leftJoin('statuses', 'bs.status_id', '=', 'statuses.id');
    }

    public function stepComments()
    {
        return $this->hasMany(StepComment::class);
    }

    /**
     * Check if a step is completed for a building with matching input source id.
     *
     * @return bool
     */
    public function hasCompleted(Step $step, InputSource $inputSource = null)
    {
        if ($inputSource instanceof InputSource) {
            return $this->completedSteps()
                        ->forInputSource($inputSource)
                        ->where('step_id', $step->id)->count() > 0;
        }

        return $this->completedSteps()
                    ->where('step_id', $step->id)->count() > 0;
    }

    /**
     * Check if all quick scan steps have been completed
     *
     * @return bool
     */
    public function hasCompletedQuickScan(InputSource $inputSource): bool
    {
        $quickScanSteps = Step::quickScan()->get();
        foreach ($quickScanSteps as $quickScanStep) {
            if (! $this->hasCompleted($quickScanStep, $inputSource)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if a building has answered any or a specific expert step
     *
     * @param  \App\Models\Step|null  $step
     *
     * @return bool
     */
    public function hasAnsweredExpertQuestion(Step $step = null): bool
    {
        $masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);

        $query = $this->completedSteps()
            ->forInputSource($masterInputSource)
            ->whereHas('step', function ($query) {
                $query->whereNotIn('short', StepHelper::QUICK_SCAN_STEP_SHORTS);
            });

        if ($step instanceof Step) {
            $query->where('step_id', $step->id);
        }

        return $query->count() > 0;
    }


    /**
     * Check if a step is not completed.
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

    public function completedSubSteps(): HasMany
    {
        return $this->hasMany(CompletedSubStep::class);
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
        if ( ! $features instanceof BuildingFeature) {
            return null;
        }
        if ( ! $features->buildingType instanceof BuildingType) {
            return null;
        }
        $example = ExampleBuilding::whereNull('cooperation_id')
                                  ->where(
                                      'buiding_type_id',
                                      $features->buildingType->id
                                  )
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
        if ( ! $example instanceof ExampleBuilding) {
            return null;
        }

        return $example->getExampleValueForYear($this->getBuildYear(), $key);
    }

    public function getBuildYear()
    {
        if ( ! $this->buildingFeatures instanceof BuildingFeature) {
            return null;
        }

        return $this->buildingFeatures->build_year;
    }

    /**
     * @param $short
     *
     * @return BuildingElement|null
     */
    public function getBuildingElement($short, InputSource $inputSource = null)
    {
        if ($inputSource instanceof InputSource) {
            return $this->buildingElements()
                        ->forInputSource($inputSource)
                        ->leftJoin(
                            'elements as e',
                            'building_elements.element_id',
                            '=',
                            'e.id'
                        )
                        ->where('e.short', $short)->first(
                    ['building_elements.*']
                );
        }

        return $this->buildingElements()
                    ->leftJoin(
                        'elements as e',
                        'building_elements.element_id',
                        '=',
                        'e.id'
                    )
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
                    ->leftJoin(
                        'elements as e',
                        'building_elements.element_id',
                        '=',
                        'e.id'
                    )
                    ->where('e.short', $short)->select(['building_elements.*']
            )->get();
    }

    /**
     * @param  string  $short
     *
     * @return BuildingService|null
     */
    public function getBuildingService($short, InputSource $inputSource)
    {
        return $this->buildingServices()
                    ->forInputSource($inputSource)
                    ->leftJoin(
                        'services as s',
                        'building_services.service_id',
                        '=',
                        's.id'
                    )
                    ->where('s.short', $short)->first(['building_services.*']);
    }

    /**
     * @param  string  $short
     *
     * @return ServiceValue|null
     */
    public function getServiceValue($short, InputSource $inputSource)
    {
        $serviceValue = null;
        /** @var BuildingService $buildingService */
        $buildingService = $this->getBuildingService($short, $inputSource);

        if ($buildingService instanceof BuildingService) {
            $serviceValue = $buildingService->serviceValue;
        }

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
     * @return BuildingType|null
     */
    public function getBuildingType(InputSource $inputSource)
    {
        $buildingFeature = $this->buildingFeatures()->forInputSource(
            $inputSource
        )->first();

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
     */
    public function privateMessages(): HasMany
    {
        return $this->hasMany(PrivateMessage::class);
    }

    /**
     * Get all the statuses from a building.
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
     * @param  string|Status  $status
     *
     * @return void
     */
    public function setStatus($status)
    {
        $statusModel = $this->resolveStatusModel($status);

        $this->buildingStatuses()->create([
            'status_id'        => $statusModel->id,
            'appointment_date' => $this->getAppointmentDate(),
        ]);
    }

    /**
     * convenient way of setting a appointment date on a building.
     *
     * @param  string
     *
     * @return void
     */
    public function setAppointmentDate($appointmentDate)
    {
        $this->buildingStatuses()->create([
            'status_id'        => $this->getMostRecentBuildingStatus(
            )->status_id,
            'appointment_date' => $appointmentDate,
        ]);
    }

    /**
     * Method to return the most recent appointment date.
     *
     * @return \Illuminate\Support\Carbon|mixed|null
     */
    public function getAppointmentDate()
    {
        return optional($this->getMostRecentBuildingStatus())->appointment_date;
    }

    public function getFirstIncompleteStep(array $extraStepsToIgnore = [], InputSource $inputSource): ?Step
    {
        $irrelevantSteps = $this->completedSteps()->forInputSource($inputSource)->pluck('step_id')->toArray();
        $irrelevantSteps = array_merge($irrelevantSteps, $extraStepsToIgnore);

        return Step::quickScan()
            ->whereNotIn('id', $irrelevantSteps)
            ->orderBy('order')
            ->first();
    }

    public function getFirstIncompleteSubStep(Step $step, array $extraSubStepsToIgnore = [], InputSource $inputSource): ?SubStep
    {
        $irrelevantSubSteps = $this->completedSubSteps()->forInputSource($inputSource)->pluck('sub_step_id')->toArray();
        $irrelevantSubSteps = array_merge($irrelevantSubSteps, $extraSubStepsToIgnore);

        $firstIncompleteSubStep = $step->subSteps()
            ->whereNotIn('id', $irrelevantSubSteps)
            ->orderBy('order')
            ->first();

        if (! $firstIncompleteSubStep instanceof SubStep) {
            $firstIncompleteSubStep = $step->subSteps()
                ->orderBy('order')
                ->first();
        }

        return $firstIncompleteSubStep;
    }
}
