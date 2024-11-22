<?php

namespace App\Livewire\Cooperation\Frontend\Tool\SimpleScan\MyPlan;

use App\Helpers\HoomdossierSession;
use App\Helpers\Sanitizers\HtmlSanitizer;
use App\Helpers\Str;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\UserActionPlanAdviceComments;
use Livewire\Component;

class Comments extends Component
{
    public Building $building;

    public InputSource $masterInputSource;
    public InputSource $currentInputSource;
    public InputSource $residentInputSource;
    public InputSource $coachInputSource;

    public ?UserActionPlanAdviceComments $residentComment = null;
    public ?UserActionPlanAdviceComments $coachComment = null;

    public array $originalAnswers = [
        'residentComment' => '',
        'coachComment' => '',
    ];
    public array $filledInAnswers = [
        'residentComment' => '',
        'coachComment' => '',
    ];

    public function mount(Building $building)
    {
        // Set needed input sources
        $this->masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
        $this->currentInputSource = HoomdossierSession::getInputSource(true);

        $this->residentInputSource = $this->currentInputSource->short === InputSource::RESIDENT_SHORT ? $this->currentInputSource : InputSource::findByShort(InputSource::RESIDENT_SHORT);
        $this->coachInputSource = $this->currentInputSource->short === InputSource::COACH_SHORT ? $this->currentInputSource : InputSource::findByShort(InputSource::COACH_SHORT);

        $this->building = $building;

        // Set comments
        $this->residentComment = UserActionPlanAdviceComments::forInputSource($this->residentInputSource)
            ->where('user_id', $this->building->user->id)->first();
        $this->filledInAnswers['residentComment'] = $this->residentComment instanceof UserActionPlanAdviceComments ? $this->residentComment->comment : '';

        $this->coachComment = UserActionPlanAdviceComments::forInputSource($this->coachInputSource)
            ->where('user_id', $this->building->user->id)->first();
        $this->filledInAnswers['coachComment'] = $this->coachComment instanceof UserActionPlanAdviceComments ? $this->coachComment->comment : '';

        $this->originalAnswers = $this->filledInAnswers;
    }

    public function render()
    {
        return view('livewire.cooperation.frontend.tool.simple-scan.my-plan.comments');
    }

    // Semi-duplicate code from Scannable
    public function resetToOriginalAnswer(string $toolQuestionShort)
    {
        $this->filledInAnswers[$toolQuestionShort] = $this->originalAnswers[$toolQuestionShort];
        $this->dispatch('reset-question', short: $toolQuestionShort);
    }

    public function saveSpecificToolQuestion(string $toolQuestionShort)
    {
        abort_if(HoomdossierSession::isUserObserving(), 403);

        if (array_key_exists($toolQuestionShort, $this->filledInAnswers)) {
            $this->validate([
                "filledInAnswers.{$toolQuestionShort}" => [
                    'string', 'max:250000',
                ],
            ]);

            $sourceShort = Str::before($toolQuestionShort, 'Comment');
            $commentShort = "{$sourceShort}Comment";
            $commentText = $this->filledInAnswers[$toolQuestionShort];
            // Sanitize HTML (just in case)
            $commentText = (new HtmlSanitizer())->sanitize($commentText);
            $inputSource = $this->{"{$sourceShort}InputSource"};

            if ($inputSource->short === $this->currentInputSource->short) {
                if ($this->{$commentShort} instanceof UserActionPlanAdviceComments) {
                    $this->{$commentShort}->update([
                        'comment' => $commentText,
                    ]);
                } else {
                    $this->{$commentShort} = UserActionPlanAdviceComments::create([
                        'user_id' => $this->building->user->id,
                        'input_source_id' => $inputSource->id,
                        'comment' => $commentText,
                    ]);
                }

                $this->originalAnswers[$toolQuestionShort] = $this->filledInAnswers[$toolQuestionShort];
            }
        }
    }
}
