<?php

namespace App\Http\Livewire\Cooperation\Frontend\Tool\SimpleScan;

use App\Helpers\HoomdossierSession;
use App\Helpers\Models\CooperationMeasureApplicationHelper;
use App\Helpers\NumberFormatter;
use App\Models\Cooperation;
use App\Models\CooperationMeasureApplication;
use App\Models\CustomMeasureApplication;
use App\Models\Mapping;
use App\Models\Scan;
use App\Models\UserActionPlanAdvice;
use App\Services\MappingService;
use Illuminate\Support\Arr;

class CustomChanges extends CustomMeasureForm
{
    public array $cooperationMeasureApplicationsFormData = [];
    public array $selectedCustomMeasureApplications = [];
    public array $selectedCooperationMeasureApplications = [];
    public array $previousSelectedState = [];

    public Scan $scan;
    public Cooperation $cooperation;

    public string $type;

    public function mount()
    {
        $this->build(HoomdossierSession::getBuilding(true));

        $this->type = CooperationMeasureApplicationHelper::getTypeForScan($this->scan);
        $this->cooperation = HoomdossierSession::getCooperation(true);

        $this->setMeasureApplications();
    }

    public function render()
    {
        return view('livewire.cooperation.frontend.tool.simple-scan.custom-changes');
    }

    public function updatedSelectedCooperationMeasureApplications($value)
    {
        // This triggers when the `$this->selectedCooperationMeasureApplications` is updated.
        abort_if(HoomdossierSession::isUserObserving(), 403);
        $key = 'cooperationMeasureApplications';

        // Let's diff with previous values, to define which index has changed
        $added = array_diff($value, $this->previousSelectedState[$key]);
        $removed = array_diff($this->previousSelectedState[$key], $value);

        $index = empty($added) ? Arr::first($removed) : Arr::first($added);
        // If removed is not empty, it's not visible, if it is empty, it is visible
        $visible = empty($removed);

        $measure = $this->cooperationMeasureApplicationsFormData[$index] ?? null;

        if (! empty($measure)) {
            $cooperationMeasureApplication = CooperationMeasureApplication::find($measure['id']);

            // No bogged data
            if ($cooperationMeasureApplication instanceof CooperationMeasureApplication) {
                // Make action plan advice for user, or update it, with the measure data and the set visibility
                $userActionPlanAdvice = $cooperationMeasureApplication->userActionPlanAdvices()
                    ->forInputSource($this->currentInputSource)
                    ->where('user_id', $this->building->user->id)
                    ->first();

                // We can't updateOrCreate, because we don't want to interfere with potential user
                // settings, e.g. category
                if ($userActionPlanAdvice instanceof UserActionPlanAdvice) {
                    $userActionPlanAdvice->update([
                        'visible' => $visible,
                    ]);
                } else {
                    $cooperationMeasureApplication->userActionPlanAdvices()
                        ->create([
                            'user_id' => $this->building->user->id,
                            'input_source_id' => $this->currentInputSource->id,
                            'category' => 'to-do',
                            'costs' => $cooperationMeasureApplication->costs,
                            'savings_money' => $cooperationMeasureApplication->savings_money,
                            'visible' => $visible,
                        ]);
                }
            }
        }

        // Update selected state
        $this->previousSelectedState[$key] = $this->selectedCooperationMeasureApplications;
    }

    public function updatedSelectedCustomMeasureApplications($value)
    {
        // This triggers when the `$this->selectedCustomMeasureApplications` is updated.
        abort_if(HoomdossierSession::isUserObserving(), 403);

        $key = 'customMeasureApplications';

        // Let's diff with previous values, to define which index has changed
        $added = array_diff($value, $this->previousSelectedState[$key]);
        $removed = array_diff($this->previousSelectedState[$key], $value);

        $index = empty($added) ? Arr::first($removed) : Arr::first($added);
        // If removed is not empty, it's not visible, if it is empty, it is visible
        $visible = empty($removed);

        $measure = $this->customMeasureApplicationsFormData[$index] ?? null;

        if (! empty($measure)) {
            $masterCustomMeasureApplication = CustomMeasureApplication::forInputSource($this->masterInputSource)
                ->where('hash', $measure['hash'])
                ->where('id', $measure['id'])
                ->first();

            if ($masterCustomMeasureApplication instanceof CustomMeasureApplication) {
                // tries to resolve the custom measure application
                // for the current input source
                // when it does not exist, it will update the master its advice.
                $customMeasureApplication = $masterCustomMeasureApplication->getSibling($this->currentInputSource);

                // There is a chance the measure is from the coach, so if that's the case we will just update
                // the master input source
                if ($customMeasureApplication instanceof CustomMeasureApplication) {
                    $customMeasureApplication->userActionPlanAdvices()
                        ->forInputSource($this->currentInputSource)
                        ->first()
                        ->update([
                            'visible' => $visible,
                        ]);
                } else {
                    $masterCustomMeasureApplication->userActionPlanAdvices()
                        ->forInputSource($this->masterInputSource)
                        ->first()
                        ->update([
                            'visible' => $visible,
                        ]);
                }
            }
        }

        // Update selected state
        $this->previousSelectedState[$key] = $this->selectedCustomMeasureApplications;
    }

    public function save(int $index)
    {
        $customMeasureApplication = $this->submit($index);

        $this->dispatchBrowserEvent('close-modal');

        $this->setMeasureApplications();
    }

    private function setMeasureApplications()
    {
        $this->customMeasureApplicationsFormData = [];
        $this->cooperationMeasureApplicationsFormData = [];

        // Retrieve the cooperation's custom measures
        $scope = "{$this->type}Measures";
        $cooperationMeasureApplications = $this->cooperation->cooperationMeasureApplications()
            ->{$scope}()
            ->with(['userActionPlanAdvices' => fn ($q) => $q->where('user_id', $this->building->user->id)->forInputSource($this->masterInputSource)])
            ->get();

        // Set the cooperation measures
        /** @var \App\Models\CooperationMeasureApplication $cooperationMeasureApplication */
        foreach ($cooperationMeasureApplications as $index => $cooperationMeasureApplication) {
            $this->cooperationMeasureApplicationsFormData[$index] = $cooperationMeasureApplication->only(['id', 'name', 'extra']);

            $costs = $cooperationMeasureApplication->costs;
            $this->cooperationMeasureApplicationsFormData[$index]['costs'] = [
                'from' => NumberFormatter::format($costs['from'] ?? '', 1),
                'to' => NumberFormatter::format($costs['to'] ?? '', 1),
            ];

            $this->cooperationMeasureApplicationsFormData[$index]['savings_money'] = NumberFormatter::format($cooperationMeasureApplication->savings_money, 1);

            // Let's see if a userActionPlanAdvice exists, so we know if it should be checked
            $userActionPlanAdvice = $cooperationMeasureApplication->userActionPlanAdvices->first();

            if ($userActionPlanAdvice instanceof UserActionPlanAdvice && $userActionPlanAdvice->visible) {
                $this->selectedCooperationMeasureApplications[] = (string) $index;
            }
        }

        // Only set custom measures if we're setting small types
        if ($this->type === CooperationMeasureApplicationHelper::SMALL_MEASURE) {
            // Retrieve the user's custom measures
            $customMeasureApplications = $this->building->customMeasureApplications()
                ->forInputSource($this->masterInputSource)
                ->with(['userActionPlanAdvices' => fn ($q) => $q->where('user_id', $this->building->user->id)->forInputSource($this->masterInputSource)])
                ->get();

            $measures = [];

            // Set the custom measures
            /** @var CustomMeasureApplication $customMeasureApplication */
            foreach ($customMeasureApplications as $index => $customMeasureApplication) {
                $this->customMeasureApplicationsFormData[$index] = $customMeasureApplication->only(['id', 'hash', 'name', 'info',]);
                $this->customMeasureApplicationsFormData[$index]['extra'] = ['icon' => 'icon-tools'];

                $userActionPlanAdvice = $customMeasureApplication->userActionPlanAdvices->first();

                if ($userActionPlanAdvice instanceof UserActionPlanAdvice) {
                    $costs = $userActionPlanAdvice->costs;

                    $this->customMeasureApplicationsFormData[$index]['costs'] = [
                        'from' => NumberFormatter::format($costs['from'] ?? '', 1),
                        'to' => NumberFormatter::format($costs['to'] ?? '', 1),
                    ];

                    $this->customMeasureApplicationsFormData[$index]['savings_money'] = NumberFormatter::format($userActionPlanAdvice->savings_money, 1);

                    if ($userActionPlanAdvice->visible) {
                        $this->selectedCustomMeasureApplications[] = (string)$index;
                    }
                }

                // As of now, a custom measure can only hold ONE mapping
                $mapping = MappingService::init()->from($customMeasureApplication)
                    //->type(MappingHelper::TYPE_CUSTOM_MEASURE_APPLICATION_MEASURE_CATEGORY)
                    ->resolveMapping()
                    ->first();
                if ($mapping instanceof Mapping) {
                    $this->customMeasureApplicationsFormData[$index]['measure_category'] = optional($mapping->mappable)->id;
                }
            }

            // Set mapped measures
            if (empty($this->measures)) {
                $this->measures = $measures;
            }

            // Append the option to add a new application
            $this->customMeasureApplicationsFormData[] = [
                'id' => null,
                'hash' => null,
                'name' => null,
                'info' => null,
                'costs' => [
                    'from' => null,
                    'to' => null,
                ],
                'savings_money' => null,
                'extra' => ['icon' => 'icon-tools'],
            ];
        }

        // We're done, let's define our selected state
        $this->previousSelectedState = [
            'customMeasureApplications' => $this->selectedCustomMeasureApplications,
            'cooperationMeasureApplications' => $this->selectedCooperationMeasureApplications,
        ];
    }
}
