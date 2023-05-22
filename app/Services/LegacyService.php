<?php

namespace App\Services;

use App\Helpers\Str;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\Step;
use App\Models\ToolQuestion;
use App\Models\User;
use App\Traits\FluentCaller;
use App\Traits\RetrievesAnswers;
use Illuminate\Database\Eloquent\Model;

class LegacyService
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

    public function getMeasureRelatedAnswers(Step $step)
    {
        $cdService = ConditionService::init()->building($this->building)->inputSource($this->inputSource);

        // NOTE: This somewhat looks like the UserCostService since it's very similar, yet not quite. This is due
        // to the 'execute-how' questions. When legacy steps die out, this service will also be removed.
        $shorts = $this->getToolQuestionShorts($step);

        foreach ($shorts as $measureId => $questionShorts) {
            $shorts[$measureId] = $this->getManyAnswers($questionShorts, false);

            foreach ($shorts[$measureId] as $short => $answer) {
                // We manually need to hide subsidy related questions, it's too complex for the frontend.
                if (Str::contains($short, 'subsidy')) {
                    $tq = ToolQuestion::findByShort($short);
                    if (! $cdService->forModel($tq)->isViewable()) {
                        unset($shorts[$measureId][$short]);
                    }
                }
            }
        }

        return $shorts;
    }

    public function getToolQuestionShorts(Step $step): array
    {
        $query = MeasureApplication::measureType(MeasureApplication::ENERGY_SAVING)
            ->where('step_id', $step->id);

        return $query->pluck('short', 'id')->map(function ($short, $id) {
            return [
                "user-costs-{$short}-own-total",
                "user-costs-{$short}-subsidy-total",
                static::getExecuteHowToolQuestionShort($short),
            ];
        })->toArray();
    }

    public static function getExecuteHowToolQuestionShort(string $measureApplicationShort): string
    {
        return "execute-{$measureApplicationShort}-how";
    }
}