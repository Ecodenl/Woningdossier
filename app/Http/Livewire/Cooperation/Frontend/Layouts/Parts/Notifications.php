<?php

namespace App\Http\Livewire\Cooperation\Frontend\Layouts\Parts;

use App\Helpers\HoomdossierSession;
use App\Models\InputSource;
use App\Services\Models\NotificationService;
use Livewire\Component;

class Notifications extends Component
{
    public $masterInputSource;
    public $building;
    public $nextUrl;
    public $types;

    public function mount($nextUrl, $types)
    {
        $this->nextUrl = $nextUrl;
        $this->types = (array) $types;
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
        $service = NotificationService::init()
            ->forInputSource($this->masterInputSource)
            ->forBuilding($this->building);

        $activeNotification = false;

        foreach ($this->types as $type) {
            if ($service->setType($type)->isActive()) {
                $activeNotification = true;
                break;
            }
        }

        if (! $activeNotification) {
            return redirect()->to($this->nextUrl);
        }
    }
}
