<?php

namespace App\Http\Livewire\Cooperation\Frontend\Layouts\Parts;

use App\Models\PrivateMessageView;
use App\Helpers\Hoomdossier;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Messages extends Component
{
    public int $messageCount = 0;
    public string $messageUrl = '';

    public function mount()
    {
        $this->messageUrl = route('cooperation.my-account.messages.edit');

        if (Hoomdossier::user()->can('access-admin') && Hoomdossier::user()->hasRoleAndIsCurrentRole(['coordinator', 'coach', 'cooperation-admin'])) {
            $this->messageUrl = route('cooperation.admin.messages.index');
        }
    }

    public function render()
    {
        if (Auth::check()) {
            $this->messageCount = PrivateMessageView::getTotalUnreadMessagesForCurrentRole();
        }

        return view('livewire.cooperation.frontend.layouts.parts.messages');
    }
}
