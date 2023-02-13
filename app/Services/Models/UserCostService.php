<?php

namespace App\Services\Models;

use App\Helpers\Conditions\Evaluators\MeasureHasSubsidy;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\Step;
use App\Models\ToolQuestion;
use App\Models\User;
use App\Services\ToolQuestionService;
use App\Traits\FluentCaller;
use App\Traits\RetrievesAnswers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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

    public function getAnswers(bool $performLegacyConversion = false): array
    {
        $shorts = $this->getToolQuestionShorts();

        foreach ($shorts as $measureId => $questionShorts) {
            $shorts[$measureId] = $this->getManyAnswers($questionShorts, ! $performLegacyConversion);
        }

        if ($performLegacyConversion) {
            // Remove subsidies if conditions not matched. This is not ideal but hopefully only temporary until we
            // refactor these static expert scan controllers
            foreach ($shorts as $measureId => $questions) {
                foreach ($questions as $short => $answer) {
                    if (Str::contains($short, 'subsidy-total')) {
                        $measure = MeasureApplication::find($measureId);
                        $value = [
                            'advisable_type' => get_class($measure),
                            'advisable_id' => $measure->id,
                        ];
                        if (! MeasureHasSubsidy::init($this->building, $this->inputSource)->evaluate($value)['bool']) {
                            unset($shorts[$measureId][$short]);
                        }
                    }
                }
            }
        }

        return $shorts;
    }

    public function sync(array $answers): void
    {
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
