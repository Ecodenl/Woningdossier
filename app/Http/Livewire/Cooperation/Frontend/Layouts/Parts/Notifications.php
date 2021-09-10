<?php

namespace App\Http\Livewire\Cooperation\Frontend\Layouts\Parts;

use App\Helpers\HoomdossierSession;
use App\Models\InputSource;
use App\Models\Notification;
use Livewire\Component;

class Notifications extends Component
{
    public $notification;

    public $masterInputSource;
    public $building;
    public $nextUrl;

    public function mount($nextUrl)
    {
        $this->nextUrl = $nextUrl;
        $this->building = HoomdossierSession::getBuilding(true);
        $this->masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
    }

    public function render()
    {
        $this->checkNotification();

        return view('livewire.cooperation.frontend.layouts.parts.notifications');
    }

    public function checkNotification()
    {
        $this->notification = \App\Models\Notification::active()->forBuilding($this->building)
            ->forInputSource($this->masterInputSource)->first();

        if (! $this->notification instanceof Notification) {
            return redirect()->to($this->nextUrl);
        }
    }
}
