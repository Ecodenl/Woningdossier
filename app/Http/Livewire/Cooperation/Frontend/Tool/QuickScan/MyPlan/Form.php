<?php

namespace App\Http\Livewire\Cooperation\Frontend\Tool\QuickScan\MyPlan;

use App\Helpers\HoomdossierSession;
use App\Helpers\StepHelper;
use App\Models\Building;
use App\Models\CustomMeasureApplication;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\UserActionPlanAdvice;
use App\Services\UserActionPlanAdviceService;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Livewire\Component;

class Form extends Component
{
    public array $cards = [
        UserActionPlanAdviceService::CATEGORY_COMPLETE => [

        ],
        UserActionPlanAdviceService::CATEGORY_TO_DO => [

        ],
        UserActionPlanAdviceService::CATEGORY_LATER => [

        ],
    ];

    /** @var Building */
    public $building;

    public $masterInputSource;
    public $currentInputSource;

    public array $new_measure = [];
    public int $investment = 0;
    public int $yearlySavings = 0;
    public int $availableSubsidy = 0;

    public string $category = '';

    // TODO: Move this to a constant helper when this is retrieved from backend
    public string $SUBSIDY_AVAILABLE = 'available';
    public string $SUBSIDY_UNAVAILABLE = 'unavailable';
    public string $SUBSIDY_UNKNOWN = 'unknown';

    protected $rules = [
        'new_measure.subject' => 'required',
        'new_measure.price.from' => 'required|numeric|min:0',
        'new_measure.price.to' => 'required|numeric|gt:new_measure.price.from',
//        'new_measure.expected_savings' => 'nullable|numeric',
    ];

    protected $listeners = [
        'cardMoved',
    ];

    // TODO: Proper map
    private $iconMap = [
        'floor-insulation' => 'icon-floor-insulation-excellent',
        'bottom-insulation' => 'icon-floor-insulation-good',
        'floor-insulation-research' => 'icon-floor-insulation-moderate',
        'cavity-wall-insulation' => 'icon-wall-insulation-excellent',
        'facade-wall-insulation' => 'icon-wall-insulation-good',
        'wall-insulation-research' => 'icon-wall-insulation-moderate',
        'glass-in-lead' => 'icon-glass-single',
        'hrpp-glass-only' => 'icon-glass-hr-p',
        'hrpp-glass-frames' => 'icon-glass-hr-dp',
        'hr3p-frames' => 'icon-glass-hr-tp',
        'crack-sealing' => 'icon-cracks-seams',
        'roof-insulation-pitched-inside' => 'icon-pitched-roof',
        'roof-insulation-pitched-replace-tiles' => 'icon-pitched-roof',
        'roof-insulation-flat-current' => 'icon-flat-roof',
        'roof-insulation-flat-replace-current' => 'icon-flat-roof',
        'high-efficiency-boiler-replace' => 'icon-central-heater',
        'heater-place-replace' => 'icon-sun-boiler',
        'solar-panels-place-replace' => 'icon-solar-panels',
        'repair-joint' => 'icon-tools',
        'clean-brickwork' => 'icon-tools',
        'impregnate-wall' => 'icon-hydronic-balance-temperature',
        'paint-wall' => 'icon-paint-job',
        'paint-wood-elements' => 'icon-paint-job',
        'replace-tiles' => 'icon-tools',
        'replace-roof-insulation' => 'icon-roof-insulation-excellent',
        'inspect-repair-roofs' => 'icon-tools',
        'replace-zinc-pitched' => 'icon-pitched-roof',
        'replace-zinc-flat' => 'icon-flat-roof',
        'ventilation-balanced-wtw' => 'icon-ventilation',
        'ventilation-decentral-wtw' => 'icon-ventilation',
        'ventilation-demand-driven' => 'icon-ventilation',
    ];

    public function mount()
    {
        $this->building = HoomdossierSession::getBuilding(true);
        $this->masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
        $this->currentInputSource = HoomdossierSession::getInputSource(true);

        foreach (UserActionPlanAdviceService::getCategories() as $category) {
            $advices = UserActionPlanAdvice::forInputSource($this->masterInputSource)
                ->where('user_id', $this->building->user->id)
                ->where('category', $category)
                ->orderBy('order')
                ->get();

            // Order in the DB could not be linear. For safe use, we set the order ourselves
            $order = 0;
            foreach ($advices as $advice) {
                $advisable = $advice->userActionPlanAdvisable;
                if ($advice->user_action_plan_advisable_type === MeasureApplication::class) {
                    $this->cards[$category][$order] = [
                        'id' => $advice->id,
                        'name' => Str::limit($advisable->measure_name, 22),
                        'icon' => $this->iconMap[$advisable->short] ?? 'icon-tools',
                        'price' => [
                            'from' => $advice->costs['from'] ?? 0,
                            'to' => $advice->costs['to'] ?? 0,
                        ],
                        // TODO: Subsidy
                        'subsidy' => $this->SUBSIDY_AVAILABLE,
                        'savings' => $advice->savings_money,
                        'info' => $advisable->measure_name,
                        'route' => StepHelper::buildStepUrl($advisable->step),
                    ];
                } else {
                    // Custom measure has input source so we must fetch the advisable from the master input source
                    if ($advice->user_action_plan_advisable_type === CustomMeasureApplication::class) {
                        $advisable = $advice->userActionPlanAdvisable()
                            ->forInputSource($this->masterInputSource)
                            ->first();
                    }

                    $this->cards[$category][$order] = [
                        'id' => $advice->id,
                        'name' => Str::limit($advisable->name, 22),
                        'icon' => 'icon-tools',
                        'price' => [
                            'from' => $advice->costs['from'] ?? 0,
                            'to' => $advice->costs['to'] ?? 0,
                        ],
                        // TODO: Subsidy
                        'subsidy' => $this->SUBSIDY_UNKNOWN,
                        'savings' => 0,
                        'info' => $advisable->name,
                    ];
                }

                ++$order;
            }
        }

        $this->recalculate();
    }

    public function render()
    {
        return view('livewire.cooperation.frontend.tool.quick-scan.my-plan.form');
    }

    public function updated($field)
    {
        $this->validateOnly($field, $this->rules);
    }

    public function submit()
    {
        $measureData = $this->validate($this->rules)['new_measure'];

        // Append card
        $this->cards[$this->category][Str::random()] = [
            'name' => $measureData['subject'],
            'icon' => 'icon-tools',
            'price' => $measureData['price'],
            'subsidy' => $this->SUBSIDY_UNKNOWN,
            'savings' => $measureData['expected_savings'] ?? 0,
        ];

        $this->dispatchBrowserEvent('close-modal');

        $this->recalculate();
    }

    public function setCategory($category)
    {
        $this->category = $category;
    }

    public function cardMoved($fromCategory, $toCategory, $id, $order)
    {
        // Get the original card object
        $cardData = Arr::where($this->cards[$fromCategory], function ($card, $order) use ($id) {
            return $card['id'] == $id;
        });

        // Structure: order => card
        if (! empty($cardData)) {
            $oldOrder = array_key_first($cardData);

            // Remove card from the original category
            unset($this->cards[$fromCategory][$oldOrder]);

            $card = $cardData[$oldOrder];

            // Get the cards for the new category
            $cards = $this->cards[$toCategory];

            $count = count($cards);
            dd($count);
            // We start from the top, so we don't overwrite everything
            for ($i = $count; $i > $order; $i--) {
                // Move cards one up
                $cards[$i + 1] = $card[$i];
            }
dd($cards);
            $cards[$order] = $card;

            $this->cards[$toCategory] = $cards;

            $this->updateAdvice($id, ['category' => $toCategory]);

            // Reorder in DB also
            $this->reorder($toCategory);
            if ($fromCategory !== $toCategory) {
                $this->reorder($fromCategory);

                // We also want to reorder the cards from the old category
                $cards = $this->cards[$fromCategory];

                $count = count($cards);
                // We start from the top, so we don't overwrite everything
                for ($i = $oldOrder; $i < $count; $i++) {
                    // Move cards one down
                    $cards[$i] = $card[$i + 1];
                }
                unset($cards[$count]);

                $this->cards[$fromCategory] = $cards;
            }

            $this->recalculate();
        }
    }

    public function recalculate()
    {
        // TODO: Get logic for this. This is a guessed placeholder
        $subsidyPercentage = 0.1;

        $minInvestment = 0;
        $maxInvestment = 0;
        $savings = 0;
        $subsidy = 0;

        foreach ($this->cards[UserActionPlanAdviceService::CATEGORY_TO_DO] as $card) {
            $from = $card['price']['from'] ?? 0;
            $to = $card['price']['to'] ?? 0;

            $minInvestment += $from;
            $maxInvestment += $to;
            $savings += $card['savings'] ?? 0;

//            if ($card['subsidy'] === $this->SUBSIDY_AVAILABLE) {
//                $subsidy += ($to - $from) * $subsidyPercentage;
//            }
        }

        $this->investment = ($maxInvestment + $minInvestment) / 2;
        $this->yearlySavings = $savings;
        $this->availableSubsidy = $subsidy;
    }

    public function reorder($category)
    {
        // Reorder for each card in the list. We don't need to check invisible items, so we don't have to check
        // any other cards
        foreach ($this->cards[$category] as $order => $card)
        {
            $this->updateAdvice($card['id'], ['order' => $order]);
        }
    }

    public function updateAdvice($id, array $update)
    {
        // TODO: logic!
//        // Get moved advice (will be for master input source)
//        $advice = UserActionPlanAdvice::allInputSources()
//            ->find($id);


        // Update for master input source
        UserActionPlanAdvice::allInputSources()
            ->find($id)->update($update);


//        // Get MY advice (trait will update the master one also, so we must fetch the model)
//        $myAdvice = UserActionPlanAdvice::forInputSource($this->currentInputSource)
//            ->where('user_id', $this->building->user->id)
//            ->where('user_action_plan_advisable_type', $advice->user_action_plan_advisable_type)
//            ->where('user_action_plan_advisable_id', $advice->user_action_plan_advisable_id)
//            ->where('step_id', $advice->step_id)
//            ->first();
//
//        // We can't update or create because if it doesn't exist we must replicate
//        if ($myAdvice instanceof UserActionPlanAdvice) {
//            $myAdvice->update($update);
//        } else {
//            $update['input_source_id'] = $this->currentInputSource->id;
//            $myAdvice = $advice->replicate()->fill($update)->save();
//        }
    }
}
