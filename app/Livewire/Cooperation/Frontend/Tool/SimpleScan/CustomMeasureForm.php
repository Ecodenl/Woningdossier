<?php

namespace App\Livewire\Cooperation\Frontend\Tool\SimpleScan;

use App\Events\CustomMeasureApplicationChanged;
use App\Helpers\HoomdossierSession;
use App\Helpers\MappingHelper;
use App\Helpers\NumberFormatter;
use App\Models\Building;
use App\Models\CustomMeasureApplication;
use App\Models\InputSource;
use App\Models\Mapping;
use App\Models\MeasureCategory;
use App\Models\UserActionPlanAdvice;
use App\Scopes\VisibleScope;
use App\Services\MappingService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Livewire\Component;

abstract class CustomMeasureForm extends Component
{
    public array $customMeasureApplicationsFormData = [];

    public Building $building;

    public Collection $measures;

    public InputSource $masterInputSource;
    public InputSource $currentInputSource;

    public array $attributeTranslations;

    protected function rules(): array
    {
        return [
            'customMeasureApplicationsFormData.*.name' => 'required',
            'customMeasureApplicationsFormData.*.info' => 'required',
            'customMeasureApplicationsFormData.*.measure_category' => [
                'nullable', 'exists:measure_categories,id',
            ],
            'customMeasureApplicationsFormData.*.costs.from' => 'required_if:customMeasureApplicationsFormData.*.hide_costs,false|numeric|min:0',
            'customMeasureApplicationsFormData.*.costs.to' => 'nullable|numeric|gte:customMeasureApplicationsFormData.*.costs.from',
            'customMeasureApplicationsFormData.*.savings_money' => 'nullable|numeric|max:999999',
        ];
    }

    public function build(Building $building): void
    {
        $this->building = $building;
        $this->masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
        $this->currentInputSource = HoomdossierSession::getInputSource(true);

        // Do it like this because otherwise the translation doesn't work
        $globalAttributeTranslations = __('validation.attributes');

        $this->attributeTranslations = [
            'customMeasureApplicationsFormData.*.name' => $globalAttributeTranslations['custom_measure_application.name'],
            'customMeasureApplicationsFormData.*.info' => $globalAttributeTranslations['custom_measure_application.info'],
            'customMeasureApplicationsFormData.*.measure_category' => $globalAttributeTranslations['custom_measure_application.measure_category'],
            'customMeasureApplicationsFormData.*.hide_costs' => $globalAttributeTranslations['custom_measure_application.hide_costs'],
            'customMeasureApplicationsFormData.*.costs.from' => $globalAttributeTranslations['custom_measure_application.costs.from'],
            'customMeasureApplicationsFormData.*.costs.to' => $globalAttributeTranslations['custom_measure_application.costs.to'],
            'customMeasureApplicationsFormData.*.savings_money' => $globalAttributeTranslations['custom_measure_application.savings_money'],
        ];

        $this->measures = MeasureCategory::all();
    }

    abstract public function save(int $index);

    public function submit(int $index, bool $dispatchRegulationUpdate = true): ?CustomMeasureApplication
    {
        // unauth the user if this happens, this means the user is just messing around.
        abort_if(HoomdossierSession::isUserObserving(), 403);

        $measure = $this->customMeasureApplicationsFormData[$index] ?? null;

        // We don't need to save each and every one every time one is saved, so we save by index
        if (! empty($measure)) {
            // We must validate on index base, so we replace the wild card with the current index
            $customRules = [];
            foreach ($this->rules() as $key => $rule) {
                $key = str_replace('*', $index, $key);
                $rule = str_replace('*', $index, $rule);

                $customRules[$key] = $rule;
            }
            $customAttributes = [];
            foreach ($this->attributeTranslations as $key => $translation) {
                $key = str_replace('*', $index, $key);

                $customAttributes[$key] = $translation;
            }

            // Before we can validate, we must convert human format to proper format
            // TODO: Check for later; perhaps we should check if the variable has 1 comma or 2 or more dots to define the used format and set the str_replace only if it's a Dutch format
            $costs = $measure['costs'] ?? [];
            $costs['from'] = NumberFormatter::mathableFormat(str_replace('.', '', $costs['from'] ?? ''), 2);
            $costs['to'] = NumberFormatter::mathableFormat(str_replace('.', '', $costs['to'] ?? ''), 2);

            if (empty($costs['to']) && ! is_numeric($costs['to'])) {
                // Similar situation as to what is explained on line 131 - 134 (savings money nullable);
                // If to isn't a numeric value, we want it to just not be set.
                unset($costs['to']);
            }

            $this->customMeasureApplicationsFormData[$index]['costs'] = $costs;
            $savingsMoney = empty($measure['savings_money']) ? 0 : $measure['savings_money'];
            $this->customMeasureApplicationsFormData[$index]['savings_money'] = NumberFormatter::mathableFormat(str_replace('.', '', $savingsMoney), 2);
            $validator = Validator::make([
                'customMeasureApplicationsFormData' => $this->customMeasureApplicationsFormData
            ], $customRules, [], $customAttributes);

            if ($validator->fails()) {
                // Validator failed, let's put it back as the user format
                $costs['from'] = NumberFormatter::formatNumberForUser($costs['from'] ?? 0);
                $costs['to'] = NumberFormatter::formatNumberForUser($costs['to'] ?? 0);
                $this->customMeasureApplicationsFormData[$index]['costs'] = $costs;
                $this->customMeasureApplicationsFormData[$index]['savings_money'] = NumberFormatter::formatNumberForUser($this->customMeasureApplicationsFormData[$index]['savings_money']);
            }

            // Validate, we don't need the data
            $measureData = $validator->validate()['customMeasureApplicationsFormData'][$index];

            if ($this->customMeasureApplicationsFormData[$index]['hide_costs']) {
                $measureData['costs'] = null;
            }

            // If the user has filled in a value for `savings_money` but then removes it again, the value will be an empty
            // string. This is seen as nullable by Livewire, so validation passes. This will cause an exception if not
            // caught, since the value in the database MUST be a decimal. It can't be null, nor an empty string.
            // Null coalescence doesn't apply to an empty string, so we check if it's numeric instead.
            $savingsMoney = $measureData['savings_money'] ?? 0;

            // Set update data for user action plan advice
            $updateData = [
                'category' => 'to-do',
                'costs' => $measureData['costs'] ?? null,
                'input_source_id' => $this->currentInputSource->id,
                'savings_money' => is_numeric($savingsMoney) ? $savingsMoney : 0
            ];

            // If a hash and ID are set, then a measure has been edited
            if (! is_null($measure['hash']) && ! is_null($measure['id'])) {
                // ID is set for master input source, so we fetch the master input source custom measure
                /** @var CustomMeasureApplication $customMeasureApplication */
                $masterCustomMeasureApplication = CustomMeasureApplication::forInputSource($this->masterInputSource)
                    ->where('hash', $measure['hash'])
                    ->where('id', $measure['id'])
                    ->first();

                // If it's not instanceof, something was borked by the user
                if ($masterCustomMeasureApplication instanceof CustomMeasureApplication) {
                    // The measure might be from the coach. We updateOrCreate to ensure it gets added to our own
                    // input source.
                    $customMeasureApplication = CustomMeasureApplication::updateOrCreate(
                        [
                            'building_id' => $this->building->id,
                            'input_source_id' => $this->currentInputSource->id,
                            'hash' => $masterCustomMeasureApplication->hash,
                        ],
                        [
                            'name' => ['nl' => $measure['name']],
                            'info' => ['nl' => $measure['info']],
                        ],
                    );
                }
            } else {
                $hash = Str::uuid();

                $customMeasureApplication = CustomMeasureApplication::create([
                    'building_id' => $this->building->id,
                    'input_source_id' => $this->currentInputSource->id,
                    'name' => ['nl' => $measure['name']],
                    'info' => ['nl' => $measure['info']],
                    'hash' => $hash,
                ]);

                $updateData['visible'] = true;
                $updateData['order'] = (UserActionPlanAdvice::forUser($this->building->user)
                    ->allInputSources()->max('order') ?? -1) + 1;
            }

            // The default "voeg onderdeel toe" also holds data, but the name will be empty. So when name empty; do not save
            if (isset($customMeasureApplication) && $customMeasureApplication instanceof CustomMeasureApplication) {
                // !important! this has to be done before the userActionPlanAdvice relation is made
                // otherwise the observer will fire when the mapping hasnt been done yet.

                // We read from the master. Therefore we need to sync to the master also.
                $from = $customMeasureApplication->getSibling($this->masterInputSource);
                $measureCategory = MeasureCategory::find($measureData['measure_category'] ?? null);
                $service = MappingService::init()
                    //->type(MappingHelper::TYPE_CUSTOM_MEASURE_APPLICATION_MEASURE_CATEGORY)
                    ->from($from);
                $measureCategory instanceof MeasureCategory
                    ? $service->sync([$measureCategory], MappingHelper::TYPE_CUSTOM_MEASURE_APPLICATION_MEASURE_CATEGORY)
                    : $service->detach();

                // Update the user action plan advice linked to this custom measure
                $customMeasureApplication
                    ->userActionPlanAdvices()
                    ->allInputSources()
                    ->withoutGlobalScope(VisibleScope::class)
                    ->updateOrCreate(
                        [
                            'user_id' => $this->building->user->id,
                            'input_source_id' => $this->currentInputSource->id,
                        ],
                        $updateData
                    );

                if ($dispatchRegulationUpdate) {
                    CustomMeasureApplicationChanged::dispatch($from);
                }

                return $customMeasureApplication;
            }
        }

        return null;
    }

    protected function loadCustomMeasures(): void
    {
        // Retrieve the user's custom measures
        $customMeasureApplications = $this->building->customMeasureApplications()
            ->forInputSource($this->masterInputSource)
            ->with(['userActionPlanAdvices' => fn ($q) => $q->where('user_id', $this->building->user->id)->forInputSource($this->masterInputSource)])
            ->get();

        // Set the custom measures
        /** @var CustomMeasureApplication $customMeasureApplication */
        foreach ($customMeasureApplications as $index => $customMeasureApplication) {
            $this->customMeasureApplicationsFormData[$index] = $customMeasureApplication->only(['id', 'hash', 'name', 'info',]);
            $this->customMeasureApplicationsFormData[$index]['extra'] = ['icon' => 'icon-tools'];
            $this->customMeasureApplicationsFormData[$index]['hide_costs'] = false;

            $userActionPlanAdvice = $customMeasureApplication->userActionPlanAdvices->first();

            if ($userActionPlanAdvice instanceof UserActionPlanAdvice) {
                $costs = $userActionPlanAdvice->costs;

                $this->customMeasureApplicationsFormData[$index]['costs'] = [
                    'from' => NumberFormatter::format($costs['from'] ?? '', 1),
                    'to' => NumberFormatter::format($costs['to'] ?? '', 1),
                ];

                if (is_null($costs)) {
                    $this->customMeasureApplicationsFormData[$index]['hide_costs'] = true;
                }

                $this->customMeasureApplicationsFormData[$index]['savings_money'] = NumberFormatter::format($userActionPlanAdvice->savings_money, 1);

                if ($userActionPlanAdvice->visible && property_exists($this, 'selectedCustomMeasureApplications')) {
                    $this->selectedCustomMeasureApplications[] = (string) $index;
                }
            }

            // As of now, a custom measure can only hold ONE mapping
            $mapping = MappingService::init()->from($customMeasureApplication)
                //->type(MappingHelper::TYPE_CUSTOM_MEASURE_APPLICATION_MEASURE_CATEGORY)
                ->resolveMapping()
                ->first();
            if ($mapping instanceof Mapping) {
                $this->customMeasureApplicationsFormData[$index]['measure_category'] = $mapping->mappable?->id;
            }
        }

        // Append the option to add a new application
        $this->customMeasureApplicationsFormData[] = [
            'id' => null,
            'hash' => null,
            'name' => null,
            'info' => null,
            'hide_costs' => false,
            'costs' => [
                'from' => null,
                'to' => null,
            ],
            'savings_money' => null,
            'extra' => ['icon' => 'icon-tools'],
        ];
    }
}
