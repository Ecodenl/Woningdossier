<?php

namespace App\Http\Livewire\Cooperation\Frontend\Tool\QuickScan\MyPlan;

use App\Helpers\HoomdossierSession;
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

    public ?UserActionPlanAdviceComments $residentComment;
    public string $residentCommentText = '';
    public ?UserActionPlanAdviceComments $coachComment;
    public string $coachCommentText = '';

    // holds the original comments and will not be editable in the frontend
    // this allows the user to reset it when he clicks cancel
    public string $originalCoachComment;
    public string $originalResidentComment;

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
        $this->residentCommentText = $this->residentComment instanceof UserActionPlanAdviceComments ? $this->residentComment->comment : '';

        $this->coachComment = UserActionPlanAdviceComments::forInputSource($this->coachInputSource)
            ->where('user_id', $this->building->user->id)->first();
        $this->coachCommentText = $this->coachComment instanceof UserActionPlanAdviceComments ? $this->coachComment->comment : '';

        $this->originalCoachComment = $this->coachCommentText;
        $this->originalResidentComment = $this->residentCommentText;
    }

    public function resetComment()
    {
        // method to reset the comments back to the original ones
        $this->coachCommentText = $this->originalCoachComment;
        $this->residentCommentText = $this->originalResidentComment;
    }

    public function render()
    {
        return view('livewire.cooperation.frontend.tool.quick-scan.my-plan.comments');
    }

    public function save(string $sourceShort)
    {
        abort_if(HoomdossierSession::isUserObserving(), 403);

        if ($sourceShort === InputSource::RESIDENT_SHORT || $sourceShort === InputSource::COACH_SHORT) {
            $commentShort = "{$sourceShort}Comment";
            $commentText = $this->{"{$sourceShort}CommentText"};
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
            }
        }
    }
}
