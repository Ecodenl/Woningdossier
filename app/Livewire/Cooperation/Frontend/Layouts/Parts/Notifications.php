<?php

namespace App\Livewire\Cooperation\Frontend\Layouts\Parts;

use App\Helpers\HoomdossierSession;
use App\Models\Building;
use App\Models\InputSource;
use App\Services\Models\NotificationService;
use Livewire\Component;

class Notifications extends Component
{
    public InputSource $masterInputSource;
    public Building $building;
    public string $nextUrl;
    public array $types;
    public bool $hasRedirected = false;

    public function mount($nextUrl, $types)
    {
        $this->nextUrl = $nextUrl;
        $this->types = (array) $types;
        $this->building = HoomdossierSession::getBuilding(true);
        $this->masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
    }

    public function render()
    {
        if (! $this->hasRedirected) {
            $this->checkNotification();
        }

        return view('livewire.cooperation.frontend.layouts.parts.notifications');
    }

    public function checkNotification()
    {
        $activeNotification = NotificationService::init()
            ->forInputSource($this->masterInputSource)
            ->forBuilding($this->building)
            ->hasActiveTypes($this->types);

        if (! $activeNotification) {
            $this->hasRedirected = true;
            return redirect()->to($this->nextUrl);
        }
    }
}
