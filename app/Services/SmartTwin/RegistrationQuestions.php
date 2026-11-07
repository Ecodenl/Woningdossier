<?php

namespace App\Services\SmartTwin;

use App\Helpers\Hoomdossier;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\ToolQuestion;
use App\Services\ToolQuestionService;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

/**
 * The questions about the dwelling that SmartTwin does not answer, asked when the resident registers.
 *
 * In a cooperation with SmartTwin the steps that hold them are not asked in the tool (see
 * Hoomdossier::smartTwinProvidesStepFor()), so the registration page and "Gebruiker toevoegen" ask
 * them instead, both required. The API accepts them as optional tool questions
 * (ToolQuestionHelper::SUPPORTED_API_SHORTS), so existing calls keep working.
 *
 * The form fields are named like the API's: tool_questions[<short>].
 *
 * Decision 2026-10-16-smarttwin-registratie-huur-koop-monument.
 */
class RegistrationQuestions
{
    public const array SHORTS = ['building-contract-type', 'monument'];

    public static function askedFor(?Cooperation $cooperation): bool
    {
        return Hoomdossier::smartTwinEnabledFor($cooperation);
    }

    /**
     * @return Collection<string, ToolQuestion> keyed by short, in the order of SHORTS
     */
    public static function questions(): Collection
    {
        return ToolQuestion::findByShorts(self::SHORTS)
            ->keyBy('short')
            ->sortBy(fn (ToolQuestion $toolQuestion) => array_search($toolQuestion->short, self::SHORTS));
    }

    /**
     * The answers a question accepts, as value => name, in the order the tool shows them.
     *
     * @return array<string, string>
     */
    public static function options(ToolQuestion $toolQuestion): array
    {
        return $toolQuestion->getQuestionValues()->pluck('name', 'value')->all();
    }

    /**
     * Validation rules for the answers, keyed by field (tool_questions.<short>).
     */
    public static function rules(bool $required): array
    {
        return self::questions()->mapWithKeys(fn (ToolQuestion $toolQuestion) => [
            "tool_questions.{$toolQuestion->short}" => [
                $required ? 'required' : 'nullable',
                Rule::in(array_map('strval', array_keys(self::options($toolQuestion)))),
            ],
        ])->all();
    }

    /**
     * Saves the given answers for each input source; the models copy them to master. A question
     * without an answer is left alone.
     *
     * @param  array<string, mixed>  $answers  keyed by short
     * @param  iterable<InputSource>  $inputSources
     */
    public static function save(Building $building, iterable $inputSources, array $answers): void
    {
        $toolQuestionService = ToolQuestionService::init()->building($building);

        foreach (self::questions() as $short => $toolQuestion) {
            $answer = $answers[$short] ?? null;
            if (is_null($answer) || $answer === '') {
                continue;
            }

            foreach ($inputSources as $inputSource) {
                $toolQuestionService->toolQuestion($toolQuestion)
                    ->currentInputSource($inputSource)
                    ->save($answer);
            }
        }
    }
}
