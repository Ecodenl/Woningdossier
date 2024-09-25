<?php

namespace App\Models;

use App\Services\Scans\ScanFlowService;
use App\Helpers\Conditions\ConditionEvaluator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Helpers\Arr;
use App\Helpers\DataTypes\Caster;
use App\Helpers\QuestionValues\QuestionValue;
use App\Helpers\StepHelper;
use App\Helpers\ToolQuestionHelper;
use App\Scopes\GetValueScope;
use App\Traits\HasMedia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use OwenIt\Auditing\Models\Audit;

/**
 * App\Models\Building
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $municipality_id
 * @property string $street
 * @property string $number
 * @property string $extension
 * @property string $city
 * @property string $postal_code
 * @property string $country_code
 * @property int|null $owner
 * @property int $primary
 * @property string $bag_addressid
 * @property string|null $bag_woonplaats_id
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
 * @property-read \App\Models\BuildingHeater|null $heater
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Media[] $media
 * @property-read int|null $media_count
 * @property-read \App\Models\Municipality|null $municipality
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
 * @method static \Database\Factories\BuildingFactory factory(...$parameters)
 * @method static \Plank\Mediable\MediableCollection|static[] get($columns = ['*'])
 * @method static Builder|Building newModelQuery()
 * @method static Builder|Building newQuery()
 * @method static \Illuminate\Database\Query\Builder|Building onlyTrashed()
 * @method static Builder|Building query()
 * @method static Builder|Building whereBagAddressid($value)
 * @method static Builder|Building whereBagWoonplaatsId($value)
 * @method static Builder|Building whereCity($value)
 * @method static Builder|Building whereCountryCode($value)
 * @method static Builder|Building whereCreatedAt($value)
 * @method static Builder|Building whereDeletedAt($value)
 * @method static Builder|Building whereExtension($value)
 * @method static Builder|Building whereHasMedia($tags = [], bool $matchAll = false)
 * @method static Builder|Building whereHasMediaMatchAll(array $tags)
 * @method static Builder|Building whereId($value)
 * @method static Builder|Building whereMunicipalityId($value)
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
    use HasFactory,
        SoftDeletes,
        HasMedia;

    public $fillable = [
        'street',
        'number',
        'city',
        'postal_code',
        'bag_addressid',
        'municipality_id',
        'bag_woonplaats_id',
        'building_coach_status_id',
        'extension',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Static methods
    public static function boot()
    {
        parent::boot();

        // The mediable only _detaches_ the media. We want to DELETE the media if set.
        static::deleting(function (Building $building) {
            // TODO: This doesn't delete the files
            $building->media()->delete();
        });
    }

    // Model methods
    public function getAddress(): string
    {
        return "{$this->street} {$this->number}{$this->extension}, {$this->postal_code} {$this->city}";
    }

    // Relations
    public function toolQuestionAnswers(): HasMany
    {
        return $this->hasMany(ToolQuestionAnswer::class);
    }

    public function customMeasureApplications(): HasMany
    {
        return $this->hasMany(CustomMeasureApplication::class);
    }

    public function municipality(): BelongsTo
    {
        return $this->belongsTo(Municipality::class);
    }

    // Unsorted
    public function getAnswerForAllInputSources(ToolQuestion $toolQuestion, bool $withMaster = false)
    {
        // TODO: See if and how we can reduce query calls here
        $inputSources = InputSource::all();

        $answers = null;
        $where = [];

        if (! $withMaster) {
            $where = [
                [
                    'input_source_id',
                    '!=',
                    $inputSources->where('short', InputSource::MASTER_SHORT)->first()->id,
                ],
            ];
        }

        // this means we should get the answer the "traditional way", in another table (not from the tool_question_answers)
        if (! is_null($toolQuestion->save_in)) {
            $saveIn = ToolQuestionHelper::resolveSaveIn($toolQuestion->save_in, $this);
            $table = $saveIn['table'];
            $column = $saveIn['column'];
            $where = array_merge($saveIn['where'], $where);

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
                $questionValues = QuestionValue::init($this->user->cooperation, $toolQuestion)
                    ->forInputSource($inputSource)
                    ->forBuilding($this)
                    ->withCustomEvaluation()
                    ->getQuestionValues()
                    ->pluck(
                        'name',
                        'value'
                    );

                // in case the saved value is actually a array, loop through them and select them one by one.
                // in most cases this isn't necessary, because the first $values at line 156 holds them all
                // weird cases like building values.
                if (is_array($value)) {
                    foreach ($value as $definitiveValue) {
                        $answer = $questionValues->isNotEmpty() && !is_null($definitiveValue) && isset($questionValues[$definitiveValue]) ? $questionValues[$definitiveValue] : $definitiveValue;
                        $answers[$inputSource->short][] = [
                            'answer' => $answer,
                            'value' => $definitiveValue,
                        ];
                    }
                } else {
                    $answer = $questionValues->isNotEmpty() && !is_null($value) && isset($questionValues[$value]) ? $questionValues[$value] : $value;
                    $answers[$inputSource->short][] = [
                        'answer' => $answer,
                        'value' => $value,
                    ];
                }
            }
        } else {
            $where['building_id'] = $this->id;
            $toolQuestionAnswers = $toolQuestion
                ->toolQuestionAnswers()
                ->allInputSources()
                ->with(['inputSource', 'toolQuestionCustomValue'])
                ->where($where)
                ->get();

            foreach ($toolQuestionAnswers as $index => $toolQuestionAnswer) {
                $answer = $toolQuestionAnswer->toolQuestionCustomValue?->name ?? $toolQuestionAnswer->answer;
                $answers[$toolQuestionAnswer->inputSource->short][$index] = [
                    'answer' => $answer,
                    'value' => $toolQuestionAnswer->toolQuestionCustomValue->short ?? $answer,
                ];
            }
        }

        // As last step, we want to clean up empty values
        foreach (($answers ?? []) as $short => $answer) {
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
        if (! is_null($toolQuestion->for_specific_input_source_id)) {
            // If a tool question has a specific input source, it won't be saved to master.
            // We override to the only allowed input source, because otherwise the answer is _always_ null
            $inputSource = $toolQuestion->forSpecificInputSource;
        }

        $answer = null;
        $where['input_source_id'] = $inputSource->id;
        // this means we should get the answer the "traditional way", in another table (not from the tool_question_answers)
        if (! is_null($toolQuestion->save_in)) {
            $saveIn = ToolQuestionHelper::resolveSaveIn($toolQuestion->save_in, $this);
            $table = $saveIn['table'];
            $column = $saveIn['column'];
            $where = array_merge($saveIn['where'], $where);

            $modelName = "App\\Models\\" . Str::studly(Str::singular($table));

            // we do a get, so we can make use of pluck on the collection, pluck can use dotted notation eg; extra.date
            $tempAnswer = $modelName::allInputSources()->where($where)->get()->pluck($column);

            // Just like with saving, an exception to the rule as these work differently
            if (in_array($toolQuestion->short, ['wood-elements', 'current-roof-types'])) {
                $answer = $tempAnswer->toArray();
            } else {
                $answer = $tempAnswer->first();
            }
        } else {
            $where['building_id'] = $this->id;
            $toolQuestionAnswers = $toolQuestion
                ->toolQuestionAnswers()
                ->allInputSources()
                ->where($where)
                ->get();

            // todo: refactor this to something sensible
            // TODO: Should we still refactor?
            if ($toolQuestion->data_type === Caster::ARRAY) {
                $answer = [];

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
    ): bool
    {
        $fileIsGeneratedByBuilding = $fileStorage->building_id == $this->id;
        $fileInputSourceIsCurrentInputSource = $fileStorage->input_source_id == $inputSource->id;

        return $fileIsGeneratedByBuilding && $fileInputSourceIsCurrentInputSource;
    }

    /**
     * Scope to return the buildings with most recent information from the building status.
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeWithRecentBuildingStatusInformation(Builder $query
    ): Builder
    {
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
     * @deprecated
     * @depends-annotations(use hasCompletedScan instead)
     * @return bool
     */
    public function hasCompletedQuickScan(InputSource $inputSource): bool
    {
        $scan = Scan::findByShort('quick-scan');
        return $this->hasCompletedScan($scan, $inputSource);
    }

    public function hasNotCompletedScan(Scan $scan, InputSource $inputSource): bool
    {
        return !$this->hasCompletedScan($scan, $inputSource);
    }

    public function hasCompletedScan(Scan $scan, InputSource $inputSource): bool
    {
        $steps = $scan->steps;
        foreach ($steps as $step) {
            if (!$this->hasCompleted($step, $inputSource)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check if a building has answered any or a specific expert step
     *
     * @param \App\Models\Step|null $step
     *
     * @return bool
     */
    public function hasAnsweredExpertQuestion(Step $step = null): bool
    {
        // TODO: Should we rename this to "hasAnsweredExpertStep"? Or maybe just use "hasCompleted"?
        $masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);

        $quickScan = Scan::findByShort('quick-scan');
        $liteScan = Scan::findByShort('lite-scan');
        $query = $this->completedSteps()
            ->forInputSource($masterInputSource)
            ->whereHas('step', function ($query) use ($quickScan, $liteScan) {
                $query->where('scan_id', '!=', $quickScan->id)
                    ->where('scan_id', '!=', $liteScan->id);
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
    public function hasNotCompleted(Step $step, InputSource $inputSource = null)
    {
        return !$this->hasCompleted($step, $inputSource);
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
     * @return HasMany
     */
    public function buildingVentilations()
    {
        return $this->hasMany(BuildingVentilation::class);
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
     * @param string $short
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

    /**
     * Method to return the most recent appointment date.
     *
     * @return \Illuminate\Support\Carbon|mixed|null
     */
    public function getAppointmentDate()
    {
        return $this->getMostRecentBuildingStatus()?->appointment_date;
    }

    public function getFirstIncompleteStep(Scan $scan, InputSource $inputSource): ?Step
    {
        $completedStepIds = $scan
            ->completedSteps()
            ->forInputSource($inputSource)
            ->forBuilding($this)
            ->pluck('step_id');

        return $scan
            ->steps()
            ->whereNotIn('id', $completedStepIds)
            ->orderBy('order')
            ->first();
    }

    public function getFirstIncompleteSubStep(Step $step, InputSource $inputSource): ?SubStep
    {
        $irrelevantSubSteps = $this->completedSubSteps()->forInputSource($inputSource)->pluck('sub_step_id')->toArray();

        $evaluator = ConditionEvaluator::init()
            ->building($this)
            ->inputSource($inputSource);

        // So, a sub step might have conditions. We need to ensure we check the conditions, else we might get
        // a wrong redirect, to a sub step we can't complete, which redirects us to the sub step after it. All while
        // there might be an uncompleted sub step after that conditional sub step. This could be confusing as the user
        // would always be redirected to the same sub step, even though it's already completed, yet still can't
        // reach their action plan.
        do {
            $firstIncompleteSubStep = $step->subSteps()
                ->whereNotIn('id', $irrelevantSubSteps)
                ->orderBy('order')
                ->first();

            if ($firstIncompleteSubStep instanceof SubStep) {
                // If we didn't pass, we add the ID as not relevant, so we don't query it a second time.
                $passes = $evaluator->evaluate($firstIncompleteSubStep->conditions ?? []);
                if (! $passes) {
                    $irrelevantSubSteps[] = $firstIncompleteSubStep->id;
                }
            } else {
                // Break the loop if there are no incomplete sub steps left.
                $passes = true;
            }
        } while(! $passes);

        // If no sub step was found, just return to the first available one. This is a fallback, and generally should
        // not happen.
        if (! $firstIncompleteSubStep instanceof SubStep) {
            $firstIncompleteSubStep = $step->subSteps()
                ->orderBy('order')
                ->first();
        }

        return $firstIncompleteSubStep;
    }
}
