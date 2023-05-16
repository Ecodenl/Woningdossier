<?php

namespace App\Services\Models;

use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\Step;
use App\Models\ToolQuestion;
use App\Models\User;
use App\Models\UserCost;
use App\Services\ConditionService;
use App\Traits\FluentCaller;
use App\Traits\RetrievesAnswers;
use Illuminate\Database\Eloquent\Model;

class UserCostService
{
    use FluentCaller,
        RetrievesAnswers;

    protected User $user;
    protected InputSource $currentInputSource;
    protected Model $advisable;

    public function __construct()
    {
        $this->inputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
    }

    public function user(User $user): self
    {
        $this->user = $user;
        $this->building = $user->building;
        return $this;
    }

    public function inputSource(InputSource $inputSource): self
    {
        $this->currentInputSource = $inputSource;
        return $this;
    }

    public function forAdvisable(Model $advisable): self
    {
        $this->advisable = $advisable;
        return $this;
    }

    public function getAnswers(): array
    {
        $shorts = $this->getToolQuestionShorts();

        foreach ($shorts as $measureId => $questionShorts) {
            $shorts[$measureId] = $this->getManyAnswers($questionShorts);
        }

        return $shorts;
    }

    public function getCost(): ?float
    {
        $userCosts = $this->user->userCosts()
            ->forInputSource($this->inputSource)
            ->whereHasMorph(
                'advisable',
                get_class($this->advisable),
                fn($q) => $q->where('advisable_id', $this->advisable->id)
            )
            ->first();

        // The user has answered a user cost question before for this measure.
        if ($userCosts instanceof UserCost) {
            // Now check if the user can answer the questions
            $ownTotalQuestion = ToolQuestion::findByShort("user-costs-{$this->advisable->short}-own-total");
            $subsidyTotalQuestion = ToolQuestion::findByShort("user-costs-{$this->advisable->short}-subsidy-total");

            $service = ConditionService::init()
                ->building($this->building)
                ->inputSource($this->inputSource);

            if ($service->forModel($ownTotalQuestion)->isViewable()) {
                $costs = $userCosts->own_total;

                if (! is_null($costs) && $service->forModel($subsidyTotalQuestion)->isViewable()) {
                    $costs = $costs - ($userCosts->subsidy_total ?? 0);
                }

                return $costs;
            }
        }

        return null;
    }

    private function getToolQuestionShorts(): array
    {
        // TODO: If we add other types, we want to support them. For now, only measure applications.
        $query = MeasureApplication::measureType(MeasureApplication::ENERGY_SAVING);

        if (isset($this->advisable)) {
            if ($this->advisable instanceof Step) {
                $query->where('step_id', $this->advisable->id);
            } else {
                $query->where('id', $this->advisable->id);
            }
        } else {
            $stepSmallMeasures = Step::findByShort('small-measures');
            $query->where('step_id', '!=', $stepSmallMeasures->id);
        }

        return $query->pluck('short', 'id')->map(function ($short, $id) {
            return [
                "user-costs-{$short}-own-total",
                "user-costs-{$short}-subsidy-total",
            ];
        })->toArray();
    }
}
