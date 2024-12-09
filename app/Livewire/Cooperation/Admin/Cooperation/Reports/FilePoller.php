<?php

namespace App\Livewire\Cooperation\Admin\Cooperation\Reports;

use App\Models\FileStorage;
use Illuminate\View\View;
use Livewire\Component;

class FilePoller extends Component
{
    public int $filesBeingProcessed;

    public function render(): View
    {
        return view('livewire.cooperation.admin.cooperation.reports.file-poller');
    }

    public function checkTotal(): void
    {
        if (FileStorage::leaveOutPersonalFiles()->withExpired()->beingProcessed()->count() < $this->filesBeingProcessed) {
            $this->dispatch('page-reload');
        }
    }
}
