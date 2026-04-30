<?php

namespace App\Livewire\Cooperation\Admin\Buildings;

use App\Helpers\ScanAvailabilityHelper;
use App\Helpers\SmallMeasuresSettingHelper;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\Scan;
use App\Services\CooperationScanService;
use Livewire\Component;

class ScanSettings extends Component
{
    public Building $building;
    public Cooperation $cooperation;
    public string $selectedScan;

    /** @var array<string, bool> */
    public array $smallMeasuresEnabled = [];

    /** @var array<string, string> */
    public array $mapping;

    /** @var array<string, string|null> */
    public array $disabledOptions = [];

    public function mount(Building $building, Cooperation $cooperation): void
    {
        $this->building = $building;
        $this->cooperation = $cooperation;
        $this->mapping = CooperationScanService::translationMap();
        $this->selectedScan = ScanAvailabilityHelper::getCurrentTypeForBuilding($building);

        $this->loadSmallMeasures();
        $this->computeDisabledOptions();
    }

    public function updateScanType(string $value): void
    {
        // Validate against disabled options
        if (isset($this->disabledOptions[$value])) {
            return;
        }

        // Validate against allowed types
        if (! array_key_exists($value, $this->mapping)) {
            return;
        }

        ScanAvailabilityHelper::syncAvailability($this->building, $value);

        // Clear small measures overrides for scans that are no longer enabled
        $enabledScanShorts = match ($value) {
            Scan::QUICK => [Scan::QUICK],
            Scan::LITE => [Scan::LITE],
            'both-scans' => [Scan::QUICK, Scan::LITE],
            default => [Scan::QUICK],
        };

        foreach (Scan::simpleScans()->get() as $scan) {
            if (! in_array($scan->short, $enabledScanShorts)) {
                SmallMeasuresSettingHelper::clearOverride($this->building, $scan);
            }
        }

        session()->flash('success', __('cooperation/admin/buildings.show.scan-availability.success'));

        $this->js('window.location.reload()');
    }

    public function toggleSmallMeasures(string $scanShort, bool $value): void
    {
        $scan = Scan::findByShort($scanShort);

        if (! $scan || $scan->isLiteScan()) {
            return;
        }

        SmallMeasuresSettingHelper::setOverride($this->building, $scan, $value);
        $this->smallMeasuresEnabled[$scanShort] = $value;

        $this->dispatch(
            'alert-flash',
            type: 'success',
            message: __('cooperation/admin/buildings.show.small-measures.success')
        );
    }

    private function loadSmallMeasures(): void
    {
        foreach (Scan::simpleScans()->get() as $scan) {
            $this->smallMeasuresEnabled[$scan->short] = SmallMeasuresSettingHelper::isEnabledForBuilding($this->building, $scan);
        }
    }

    private function computeDisabledOptions(): void
    {
        $this->disabledOptions = [];

        // Check if lite-scan can be enabled
        $liteScan = Scan::lite();
        if ($liteScan) {
            $canEnableLite = ScanAvailabilityHelper::canEnable($this->building, $liteScan);
            if ($canEnableLite !== true) {
                $this->disabledOptions[Scan::LITE] = __($canEnableLite);
                $this->disabledOptions['both-scans'] = __($canEnableLite);
            }
        }
    }

    public function render()
    {
        return view('livewire.cooperation.admin.buildings.scan-settings');
    }
}
