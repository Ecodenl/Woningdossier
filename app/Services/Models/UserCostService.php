<?php

namespace App\Services\Models;

use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\Step;
use App\Models\ToolQuestion;
use App\Models\User;
use App\Services\ToolQuestionService;
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

    public function sync(array $answers): void
    {
        \Log::debug('Saving', $answers);
        // TODO: If we add other types, we want to support them. For now, only measure applications.
        foreach ($answers as $short => $answer) {
            $short = str_replace('_', '-', $short);
            $toolQuestionShort = "user-costs-{$this->advisable->short}-{$short}";
            $toolQuestion = ToolQuestion::findByShort($toolQuestionShort);
            ToolQuestionService::init($toolQuestion)->building($this->building)
                ->currentInputSource($this->currentInputSource)
                ->save($answer);
        }
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
