<?php

namespace Tests\Unit\app\Models;

use App\Models\ToolQuestion;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

/**
 * Where SmartTwin drives the scan nothing Hoomdossier still asks is mandatory: the data the scan used
 * to insist on is entered in SmartTwin and comes back through the mapping. Which scans that is, is
 * Hoomdossier::smartTwinDrivesScan()'s to say (see HoomdossierSmartTwinTest); the question is told.
 *
 * The subtlety worth pinning down is that 'required' is swapped for 'nullable' rather than dropped.
 * Dropping it would leave the rules behind it to run against an empty answer, turning "you have to
 * answer this" into "your non-answer is invalid" -- which is worse, because there is no way out.
 */
final class ToolQuestionRequiredTest extends TestCase
{
    private function question(array $validation): ToolQuestion
    {
        return new ToolQuestion(['short' => 'probe', 'validation' => $validation]);
    }

    private function acceptsNoAnswer(ToolQuestion $question, bool $smartTwinDrivesScan): bool
    {
        return Validator::make(['answer' => null], ['answer' => $question->validationRules($smartTwinDrivesScan)])->passes();
    }

    public function test_without_smart_twin_a_required_question_stays_required(): void
    {
        $question = $this->question(['required', 'in:1,2,3']);

        $this->assertTrue($question->isRequired(false));
        $this->assertSame(['required', 'in:1,2,3'], $question->validationRules(false));
        $this->assertFalse($this->acceptsNoAnswer($question, false));
    }

    public function test_with_smart_twin_nothing_is_required(): void
    {
        $question = $this->question(['required', 'in:1,2,3']);

        $this->assertFalse($question->isRequired(true));
        $this->assertTrue($this->acceptsNoAnswer($question, true));
    }

    public function test_required_is_swapped_for_nullable_rather_than_dropped(): void
    {
        // Dropping it would leave ['in:1,2,3'], which rejects null.
        $this->assertSame(['nullable', 'in:1,2,3'], $this->question(['required', 'in:1,2,3'])->validationRules(true));
    }

    public function test_the_rules_behind_an_answer_still_apply(): void
    {
        $question = $this->question(['required', 'in:1,2,3']);

        // Optional is not the same as unchecked: an answer that is given still has to make sense.
        $this->assertFalse(
            Validator::make(['answer' => 9], ['answer' => $question->validationRules(true)])->passes()
        );
        $this->assertTrue(
            Validator::make(['answer' => 2], ['answer' => $question->validationRules(true)])->passes()
        );
    }

    public function test_a_question_that_was_never_required_also_accepts_no_answer(): void
    {
        // Without 'nullable' of its own, 'integer' would reject an answer that was not given.
        $question = $this->question(['integer']);

        $this->assertSame(['nullable', 'integer'], $question->validationRules(true));
        $this->assertTrue($this->acceptsNoAnswer($question, true));
    }

    public function test_an_existing_nullable_is_not_doubled(): void
    {
        $this->assertSame(
            ['nullable', 'string', 'max:2000'],
            $this->question(['nullable', 'string', 'max:2000'])->validationRules(true),
        );
    }
}
