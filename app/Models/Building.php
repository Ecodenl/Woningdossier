<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Helpers\Conditions\ConditionEvaluator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Helpers\Arr;
use App\Helpers\DataTypes\Caster;
use App\Helpers\QuestionValues\QuestionValue;
use App\Helpers\ToolQuestionHelper;
use App\Scopes\GetValueScope;
use App\Traits\HasMedia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Plank\Mediable\MediableInterface;

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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BuildingCoachStatus> $buildingCoachStatuses
 * @property-read int|null $building_coach_statuses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BuildingElement> $buildingElements
 * @property-read int|null $building_elements_count
 * @property-read \App\Models\BuildingFeature|null $buildingFeatures
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BuildingNotes> $buildingNotes
 * @property-read int|null $building_notes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BuildingPermission> $buildingPermissions
 * @property-read int|null $building_permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BuildingService> $buildingServices
 * @property-read int|null $building_services_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BuildingStatus> $buildingStatuses
 * @property-read int|null $building_statuses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BuildingVentilation> $buildingVentilations
 * @property-read int|null $building_ventilations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CompletedStep> $completedSteps
 * @property-read int|null $completed_steps_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CompletedSubStep> $completedSubSteps
 * @property-read int|null $completed_sub_steps_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BuildingInsulatedGlazing> $currentInsulatedGlazing
 * @property-read int|null $current_insulated_glazing_count
 * @property-read \App\Models\BuildingPaintworkStatus|null $currentPaintworkStatus
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CustomMeasureApplication> $customMeasureApplications
 * @property-read int|null $custom_measure_applications_count
 * @property-read \App\Models\BuildingHeater|null $heater
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \App\Models\Municipality|null $municipality
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PrivateMessage> $privateMessages
 * @property-read int|null $private_messages_count
 * @property-read \App\Models\BuildingPvPanel|null $pvPanels
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\QuestionsAnswer> $questionAnswers
 * @property-read int|null $question_answers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BuildingRoofType> $roofTypes
 * @property-read int|null $roof_types_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StepComment> $stepComments
 * @property-read int|null $step_comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ToolQuestionAnswer> $toolQuestionAnswers
 * @property-read int|null $tool_question_answers_count
 * @property-read \App\Models\User|null $user
 * @method static \Plank\Mediable\MediableCollection<int, static> all($columns = ['*'])
 * @method static \Database\Factories\BuildingFactory factory($count = null, $state = [])
 * @method static \Plank\Mediable\MediableCollection<int, static> get($columns = ['*'])
 * @method static Builder<static>|Building newModelQuery()
 * @method static Builder<static>|Building newQuery()
 * @method static Builder<static>|Building onlyTrashed()
 * @method static Builder<static>|Building query()
 * @method static Builder<static>|Building whereBagAddressid($value)
 * @method static Builder<static>|Building whereBagWoonplaatsId($value)
 * @method static Builder<static>|Building whereCity($value)
 * @method static Builder<static>|Building whereCountryCode($value)
 * @method static Builder<static>|Building whereCreatedAt($value)
 * @method static Builder<static>|Building whereDeletedAt($value)
 * @method static Builder<static>|Building whereExtension($value)
 * @method static Builder<static>|Building whereHasMedia($tags = [], bool $matchAll = false)
 * @method static Builder<static>|Building whereHasMediaMatchAll($tags)
 * @method static Builder<static>|Building whereId($value)
 * @method static Builder<static>|Building whereMunicipalityId($value)
 * @method static Builder<static>|Building whereNumber($value)
 * @method static Builder<static>|Building whereOwner($value)
 * @method static Builder<static>|Building wherePostalCode($value)
 * @method static Builder<static>|Building wherePrimary($value)
 * @method static Builder<static>|Building whereStreet($value)
 * @method static Builder<static>|Building whereUpdatedAt($value)
 * @method static Builder<static>|Building whereUserId($value)
 * @method static Builder<static>|Building withMedia($tags = [], bool $matchAll = false, bool $withVariants = false)
 * @method static Builder<static>|Building withMediaAndVariants($tags = [], bool $matchAll = false)
 * @method static Builder<static>|Building withMediaAndVariantsMatchAll($tags = [])
 * @method static Builder<static>|Building withMediaMatchAll(bool $tags = [], bool $withVariants = false)
 * @method static Builder<static>|Building withRecentBuildingStatusInformation()
 * @method static Builder<static>|Building withTrashed()
 * @method static Builder<static>|Building withoutTrashed()
 * @mixin \Eloquent
 */
class Building extends Model implements MediableInterface
{
    use HasFactory,
        SoftDeletes,
        HasMedia;

    public $fillable = [
        'municipality_id',
        'street',
        'number',
        'extension',
        'city',
        'postal_code',
        //'country_code',
        //'owner',
        //'primary',
        'bag_addressid',
        'bag_woonplaats_id',
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
    public function getAnswerForAllInputSources(ToolQuestion $toolQuestion, bool $withMaster = false): array
    {
        // TODO: See if and how we can reduce query calls here
        $inputSources = InputSource::all();

        $answers = [];
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

        // This means we should get the answer the "traditional way", from another
        // table (not from the tool_question_answers)
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
                        $answer = $questionValues->isNotEmpty() && ! is_null($definitiveValue) && isset($questionValues[$definitiveValue])
                            ? $questionValues[$definitiveValue]
                            : $definitiveValue;
                        $answers[$inputSource->short][] = [
                            'answer' => $answer,
                            'value' => $definitiveValue,
                        ];
                    }
                } else {
                    $answer = $questionValues->isNotEmpty() && ! is_null($value) && isset($questionValues[$value])
                        ? $questionValues[$value]
                        : $value;
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
                $answer = $toolQuestionAnswer->toolQuestionCustomValue->name ?? $toolQuestionAnswer->answer;
                $answers[$toolQuestionAnswer->inputSource->short][$index] = [
                    'answer' => $answer,
                    'value' => $toolQuestionAnswer->toolQuestionCustomValue->short ?? $answer,
                ];
            }
        }

        // As last step, we want to clean up empty values
        foreach ($answers as $short => $answer) {
            if (Arr::isWholeArrayEmpty($answer)) {
                unset($answers[$short]);
            }
        }

        return $answers;
    }

    public function getAnswer(InputSource $inputSource, ToolQuestion $toolQuestion): mixed
    {
        if (! is_null($toolQuestion->for_specific_input_source_id)) {
            // If a tool question has a specific input source, it won't be saved to master.
            // We override to the only allowed input source, because otherwise the answer is _always_ null
            $inputSource = $toolQuestion->forSpecificInputSource;
        }

        $answer = null;
        $where['input_source_id'] = $inputSource->id;
        // This means we should get the answer the "traditional way", from another
        // table (not from the tool_question_answers).
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
     */
    public function scopeWithRecentBuildingStatusInformation(Builder $query): Builder
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

    public function stepComments(): HasMany
    {
        return $this->hasMany(StepComment::class);
    }

    /**
     * Check if a step is completed for a building with matching input source id.
     */
    public function hasCompleted(Step $step, InputSource $inputSource = null): bool
    {
        if ($inputSource instanceof InputSource) {
            return $this->completedSteps()
                ->forInputSource($inputSource)
                ->where('step_id', $step->id)->count() > 0;
        }

        return $this->completedSteps()
            ->where('step_id', $step->id)->count() > 0;
    }

    public function hasNotCompletedScan(Scan $scan, InputSource $inputSource): bool
    {
        return ! $this->hasCompletedScan($scan, $inputSource);
    }

    public function hasCompletedScan(Scan $scan, InputSource $inputSource): bool
    {
        $steps = $scan->steps;
        foreach ($steps as $step) {
            if (! $this->hasCompleted($step, $inputSource)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check if a building has answered any or a specific expert step
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
     */
    public function hasNotCompleted(Step $step, InputSource $inputSource = null): bool
    {
        return ! $this->hasCompleted($step, $inputSource);
    }

    /**
     * Returns the user progress.
     */
    public function completedSteps(): HasMany
    {
        return $this->hasMany(CompletedStep::class);
    }

    public function completedSubSteps(): HasMany
    {
        return $this->hasMany(CompletedSubStep::class);
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function buildingFeatures(): HasOne
    {
        return $this->hasOne(BuildingFeature::class);
    }

    /**
     * Return all the building notes.
     */
    public function buildingNotes(): HasMany
    {
        return $this->hasMany(BuildingNotes::class);
    }

    public function buildingElements(): HasMany
    {
        return $this->hasMany(BuildingElement::class);
    }

    public function buildingVentilations(): HasMany
    {
        return $this->hasMany(BuildingVentilation::class);
    }

    public function getBuildingElement(string $short, InputSource $inputSource = null): ?BuildingElement
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

        /** @var null|BuildingElement */
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
            ->where('e.short', $short)->select(['building_elements.*'])->get();
    }

    public function getBuildingService(string $short, InputSource $inputSource): ?BuildingService
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

    public function buildingServices(): HasMany
    {
        return $this->hasMany(BuildingService::class);
    }

    /**
     * Return the building type from a builing through the building features.
     */
    public function getBuildingType(InputSource $inputSource): ?BuildingType
    {
        $buildingFeature = $this->buildingFeatures()->forInputSource(
            $inputSource
        )->first();

        if ($buildingFeature instanceof BuildingFeature) {
            return $buildingFeature->buildingType;
        }

        return null;
    }

    public function currentInsulatedGlazing(): HasMany
    {
        return $this->hasMany(BuildingInsulatedGlazing::class);
    }

    public function currentPaintworkStatus(): HasOne
    {
        return $this->hasOne(BuildingPaintworkStatus::class);
    }

    public function pvPanels(): HasOne
    {
        return $this->hasOne(BuildingPvPanel::class);
    }

    public function heater(): HasOne
    {
        return $this->hasOne(BuildingHeater::class);
    }

    /**
     * Returns all roof types of this building. Get the primary via the
     * building features.
     */
    public function roofTypes(): HasMany
    {
        return $this->hasMany(BuildingRoofType::class);
    }

    /**
     * Get all the coach statuses for a building.
     */
    public function buildingCoachStatuses(): HasMany
    {
        return $this->hasMany(BuildingCoachStatus::class);
    }

    public function buildingPermissions(): HasMany
    {
        return $this->hasMany(BuildingPermission::class);
    }

    /**
     * Get all the answers for the building.
     */
    public function questionAnswers(): HasMany
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
     */
    public function getMostRecentBuildingStatus(): ?BuildingStatus
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

        /** @var null|Step */
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
        } while (! $passes);

        // If no sub step was found, just return to the first available one. This is a fallback, and generally should
        // not happen.
        if (! $firstIncompleteSubStep instanceof SubStep) {
            $firstIncompleteSubStep = $step->subSteps()
                ->orderBy('order')
                ->first();
        }

        /** @var null|\App\Models\SubStep $firstIncompleteSubStep */
        return $firstIncompleteSubStep;
    }
}
