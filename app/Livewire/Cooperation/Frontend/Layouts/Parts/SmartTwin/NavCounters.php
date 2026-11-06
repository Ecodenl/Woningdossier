<?php

namespace App\Livewire\Cooperation\Frontend\Layouts\Parts\SmartTwin;

use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Helpers\MediaHelper;
use App\Models\Building;
use App\Models\Media;
use App\Models\PrivateMessage;
use App\Models\PrivateMessageView;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

/**
 * The two navigation items that carry a count. They share a component so the navigation polls once
 * rather than twice; they sit next to each other in the design, so nothing is gained by splitting
 * them up.
 */
class NavCounters extends Component
{
    public int $messageCount = 0;
    public int $fileCount = 0;
    public string $messageUrl = '';
    public string $fileUrl = '';
    public bool $canViewFiles = false;
    public bool $canViewMessages = false;

    public function mount(string $fileUrl): void
    {
        $this->fileUrl = $fileUrl;

        $this->messageUrl = route('cooperation.my-account.messages.edit');

        $user = Hoomdossier::user();

        if ($user->can('access-admin')
            && $user->hasRoleAndIsCurrentRole(['coordinator', 'coach', 'cooperation-admin'])) {
            $this->messageUrl = route('cooperation.admin.messages.index');
        }
    }

    public function render(): View
    {
        if (Auth::check()) {
            // Both items are permission bound, and not by the same permission. They're resolved here
            // rather than around the component in the navigation, so that being denied the files
            // does not also take the messages down with it.
            $account = Hoomdossier::account();
            $building = HoomdossierSession::getBuilding(true);

            $this->canViewFiles = $building instanceof Building
                && $account?->can('viewAny', [Media::class, HoomdossierSession::getInputSource(true), $building]);
            $this->canViewMessages = (bool) $account?->can('viewAny', PrivateMessage::class);

            if ($this->canViewMessages) {
                $this->messageCount = PrivateMessageView::getTotalUnreadMessagesForCurrentRole();
            }
            if ($this->canViewFiles) {
                $this->fileCount = $this->countFiles();
            }
        }

        return view('livewire.cooperation.frontend.layouts.parts.smart-twin.nav-counters');
    }

    /**
     * The files on the building that the file overview shows to whoever is looking: the tags a
     * resident may pick, and of those only the ones the media policy lets them view. A coach does
     * not see a resident's file that was not shared with the cooperation, so it does not count
     * towards their label either.
     *
     * A total, deliberately: it says how many files there are, not how many are new. That is why
     * it is written into the label instead of shown as a badge; the badge on the messages item
     * counts unread, and one badge meaning two things was confusing.
     */
    private function countFiles(): int
    {
        $building = HoomdossierSession::getBuilding(true);
        $account = Hoomdossier::account();

        if (! $building instanceof Building || is_null($account)) {
            return 0;
        }

        $inputSource = HoomdossierSession::getInputSource(true);

        // Through the policy rather than a query of its own, so the count cannot drift from the
        // overview when the rules for who sees which file change.
        /** @var Collection<int, Media> $files */
        $files = $building->media()
            ->wherePivotIn('tag', MediaHelper::getFillableTagsForClass(Building::class))
            ->get();

        return $files
            ->filter(fn (Media $file) => $account->can('view', [$file, $inputSource]))
            ->count();
    }
}
