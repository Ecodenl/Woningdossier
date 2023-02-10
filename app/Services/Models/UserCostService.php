<?php

namespace App\Services\Models;

use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\Step;
use App\Models\User;
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

    public function __construct(User $user, InputSource $currentInputSource)
    {
        $this->user = $user;
        $this->building = $user->building;
        $this->currentInputSource = $currentInputSource;
        $this->inputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
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
