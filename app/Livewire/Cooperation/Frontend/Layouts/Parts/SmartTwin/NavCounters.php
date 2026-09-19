<?php

namespace App\Livewire\Cooperation\Frontend\Layouts\Parts\SmartTwin;

use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Helpers\MediaHelper;
use App\Models\Building;
use App\Models\Media;
use App\Models\PrivateMessage;
use App\Models\PrivateMessageView;
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
     * Every file on the building the resident may pick a tag for, which is the same set the file
     * overview lists.
     *
     * NOTE: this is a total, not a "new since you last looked". Media has no seen state to build
     * that on, so if the badge is meant to signal unseen files it needs tracking that does not
     * exist yet.
     */
    private function countFiles(): int
    {
        $building = HoomdossierSession::getBuilding(true);

        if (! $building instanceof Building) {
            return 0;
        }

        return $building->media()
            ->wherePivotIn('tag', MediaHelper::getFillableTagsForClass(Building::class))
            ->count();
    }
}
