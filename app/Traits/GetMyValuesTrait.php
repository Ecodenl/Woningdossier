<?php

namespace App\Traits;

use App\Helpers\Cache\BaseCache;
use App\Helpers\DataTypes\Caster;
use App\Helpers\HoomdossierSession;
use App\Models\Building;
use App\Models\BuildingRoofType;
use App\Models\CompletedStep;
use App\Models\CompletedSubStep;
use App\Models\CooperationMeasureApplication;
use App\Models\CustomMeasureApplication;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\Notification;
use App\Models\ToolQuestion;
use App\Models\ToolQuestionAnswer;
use App\Models\User;
use App\Models\UserActionPlanAdvice;
use App\Scopes\GetValueScope;
use App\Scopes\VisibleScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

trait GetMyValuesTrait
{
    /**
     * Boot this trait.
     * (https://www.archybold.com/blog/post/booting-eloquent-model-traits)
     */
    public static function bootGetMyValuesTrait()
    {
        static::saved(function (Model $model) {
            // might be handy to prevent getting into an infinite loop (-:>
            if (! in_array(($model->inputSource->short ?? ''), [InputSource::MASTER_SHORT, InputSource::EXAMPLE_BUILDING_SHORT, InputSource::EXTERNAL_SHORT])) {
                $model->saveForMasterInputSource();
            }
        });

        static::deleting(function (Model $model) {
            // might be handy to prevent getting into an infinite loop (-:>
            if (! in_array(($model->inputSource->short ?? ''), [InputSource::MASTER_SHORT, InputSource::EXAMPLE_BUILDING_SHORT, InputSource::EXTERNAL_SHORT])) {
                $supportedClasses = [
                    BuildingRoofType::class,
                    ToolQuestionAnswer::class,
                    CompletedStep::class,
                    CompletedSubStep::class,
                    Notification::class,
                ];

                // TODO: This needs to work for all models.
                //TODO: We need a way to detect if we _should_ delete. A row can exist for multiple sources, so
                // deleting one should only delete the master if there's no other row.
                if (in_array(get_class($model), $supportedClasses)) {
                    $model->deleteForMasterInputSource();
                }
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
        return \App\Helpers\Cache\Schema::hasColumn($this->getTable(), $attribute);
    }

    protected function saveForMasterInputSource()
    {
        $tablesToIgnore = [
            'user_action_plan_advice_comments', 'step_comments', 'private_message_views', 'file_storages'
        ];

        // Sometimes we won't need the master input source, so we will ignore these
        if (! in_array($this->getTable(), $tablesToIgnore)) {
            $masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
            $data = $this->attributesToArray();
            // In case there's attributes we don't want to copy over
            foreach (($this->ignoreAttributes ?? []) as $ignoreAttribute) {
                unset($data[$ignoreAttribute]);
            }

            $data['input_source_id'] = $masterInputSource->id;
            unset($data['id']);

            $wheres = [
                'input_source_id' => $masterInputSource->id,
            ];

            $crucialRelationCombinationIds = [
                'user_id', 'building_id', 'tool_question_id', 'tool_question_custom_value_id', 'element_id',
                'service_id', 'hash', 'sub_step_id', 'short', 'step_id', 'interested_in_type', 'interested_in_id',
                'considerable_id', 'considerable_type', 'question_id', 'questionnaire_id', 'uuid', 'advisable_type',
                'advisable_id',
            ];
            $crucialRelationCombinationIds = array_merge($crucialRelationCombinationIds, $this->crucialRelations ?? []);

            if ($this instanceof UserActionPlanAdvice) {
                // TODO: Should this check the model input source (`->forInputSource($this->inputSource)`)?
                $advisable = $this->userActionPlanAdvisable;
                if ($advisable instanceof MeasureApplication || $advisable instanceof CooperationMeasureApplication) {
                    $crucialRelationCombinationIds[] = 'user_action_plan_advisable_id';
                    $crucialRelationCombinationIds[] = 'user_action_plan_advisable_type';
                } else {
                    $advisable = $this->userActionPlanAdvisable()
                        ->forInputSource($this->inputSource)
                        ->first();

                    if ($advisable instanceof CustomMeasureApplication) {
                        // find sibling of the user one with admin input source
                        $sibling = $advisable->getSibling($masterInputSource);
                        $data['user_action_plan_advisable_id'] = $sibling->id;
                        $wheres['user_action_plan_advisable_id'] = $sibling->id;
                        $crucialRelationCombinationIds[] = 'user_action_plan_advisable_type';
                    }
                }
            }

            foreach ($crucialRelationCombinationIds as $crucialRelationCombinationId) {
                if ($this->hasAttribute($crucialRelationCombinationId)) {
                    $shouldAdd = $crucialRelationCombinationId !== 'tool_question_custom_value_id';

                    if (! $shouldAdd) {
                        // Conditional logic, tool_question_custom_value_id should only be evaluated if the
                        // question is a checkbox
                        if (! empty($data['tool_question_id']) && ($toolQuestion = ToolQuestion::find($data['tool_question_id'])) instanceof ToolQuestion) {
                            $shouldAdd = $toolQuestion->data_type == Caster::ARRAY;
                        }
                    }

                    if ($shouldAdd) {
                        $wheres[$crucialRelationCombinationId] = $this->getAttributeValue($crucialRelationCombinationId);
                    }
                }
            }

            ($this)::withoutGlobalScope(VisibleScope::class)
                ->allInputSources()
                ->updateOrCreate(
                    $wheres,
                    $data,
                );
        }

    }

    protected function deleteForMasterInputSource()
    {
        $tablesToIgnore = [
            'user_action_plan_advice_comments', 'step_comments',
        ];

        // Sometimes we won't need the master input source, so we will ignore these
        if (! in_array($this->getTable(), $tablesToIgnore)) {
            // When deleting a model (DB row), we need to find the corresponding master one, so we start with doing some
            // similar things as within the save function
            // TODO: Due to the similarities, maybe we can make this DRY?
            $masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);

            $wheres = [
                'input_source_id' => $masterInputSource->id,
            ];

            $crucialRelationCombinationIds = [
                'user_id', 'building_id', 'tool_question_id', 'tool_question_custom_value_id', 'element_id',
                'service_id', 'hash', 'sub_step_id', 'short', 'step_id', 'interested_in_type', 'interested_in_id',
                'considerable_id', 'considerable_type', 'question_id', 'questionnaire_id', 'uuid', 'advisable_type',
                'advisable_id',
            ];
            $crucialRelationCombinationIds = array_merge($crucialRelationCombinationIds, $this->crucialRelations ?? []);

            foreach ($crucialRelationCombinationIds as $crucialRelationCombinationId) {
                if ($this->hasAttribute($crucialRelationCombinationId)) {
                    $shouldAdd = $crucialRelationCombinationId !== 'tool_question_custom_value_id';

                    if (! $shouldAdd) {
                        // Conditional logic, tool_question_custom_value_id should only be evaluated if the
                        // question is a checkbox
                        if (! empty($data['tool_question_id']) && ($toolQuestion = ToolQuestion::find($data['tool_question_id'])) instanceof ToolQuestion) {
                            $shouldAdd = $toolQuestion->data_type = Caster::ARRAY;
                        }
                    }

                    if ($shouldAdd) {
                        $wheres[$crucialRelationCombinationId] = $this->getAttributeValue($crucialRelationCombinationId);
                    }
                }
            }

            $modelToDelete = ($this)::withoutGlobalScope(VisibleScope::class)
                ->allInputSources()
                ->where($wheres)
                ->first();

            if ($modelToDelete instanceof static) {
                $modelToDelete->delete();
            }
        }
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


    public function scopeForBuilding(Builder $query, $building)
    {
        $id = $building instanceof Building ? $building->id : $building;

        return $query->where('building_id', $id);
    }

    public function scopeForUser(Builder $query, $user)
    {
        $id = $user instanceof User ? $user->id : $user;

        return $query->where('user_id', $id);
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
        $currentTable = $this->getTable();

        // determine which column we should use.
        if (Schema::hasColumn($currentTable, 'building_id')) {
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
        $residentInputSource = InputSource::findByShort(InputSource::RESIDENT_SHORT);

        return $query->where('input_source_id', $residentInputSource->id);
    }

    /**
     * Check on a collection that comes from the forMe() scope if it contains a
     * Coach input source.
     */
    public static function hasCoachInputSource(Collection $inputSourcesForMe): bool
    {
        $coachInputSource = InputSource::findByShort(InputSource::COACH_SHORT);
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
        $residentInputSource = InputSource::findByShort(InputSource::RESIDENT_SHORT);
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
        $coachInputSource = InputSource::findByShort(InputSource::COACH_SHORT);
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
        $residentInputSource = InputSource::findByShort(InputSource::RESIDENT_SHORT);

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
