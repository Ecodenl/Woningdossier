<?php

namespace App\Http\Livewire\Cooperation\Frontend\Tool\QuickScan\MyPlan;

use App\Helpers\HoomdossierSession;
use App\Models\Building;
use App\Models\MeasureApplication;
use App\Models\UserActionPlanAdvice;
use App\Services\UserActionPlanAdviceService;
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
        $this->currentInputSource = HoomdossierSession::getInputSource(true);

        $advices = UserActionPlanAdvice::forInputSource($this->currentInputSource)
            ->where('user_id', $this->building->user->id)
            ->get();

        foreach (UserActionPlanAdviceService::getCategories() as $category) {
            foreach ($advices->where('category', $category) as $advice) {
                $advisable = $advice->userActionPlanAdvisable;
                if ($advice->user_action_plan_advisable_type === MeasureApplication::class) {

                    $this->cards[$category][$advice->id] = [
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
                    ];
                } else {
                    $this->cards[$category][$advice->id] = [
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
        $card = $this->cards[$fromCategory][$id] ?? null;
        // Remove card from the original category
        unset($this->cards[$fromCategory][$id]);

        // If the card is set...
        if (! empty($card)) {
            // Get the cards for the new category
            $cards = $this->cards[$toCategory];

            // Split cards at order
            $firstPart = array_slice($cards, 0, $order, true);
            $secondPart = array_slice($cards, $order, null, true);

            // Insert card at position
            $firstPart[$id] = $card;
            // Rebuild
            $cards = $firstPart + $secondPart;

            $this->cards[$toCategory] = $cards;
        }

        $this->recalculate();
    }

    public function recalculate()
    {
        return;
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
}
