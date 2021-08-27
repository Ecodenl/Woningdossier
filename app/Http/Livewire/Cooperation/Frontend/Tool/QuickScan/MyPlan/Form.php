<?php

namespace App\Http\Livewire\Cooperation\Frontend\Tool\QuickScan\MyPlan;

use App\Helpers\HoomdossierSession;
use App\Helpers\StepHelper;
use App\Models\Building;
use App\Models\InputSource;
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

    public $masterInputSource;
    public $currentInputSource;

    public array $new_measure = [];

    // Details
    public $expectedInvestment = 0;
    public $yearlySavings = 0;
    public $availableSubsidy = 0;

    // Sliders
    public $comfort = 0;
    public $renewable = 0;
    public $investment = 0;

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

    private $calculationMap = [
        'comfort' => [
            [
                'condition' => [
                    'to' => 10,
                ],
                'value' => 1,
            ],
            [
                'condition' => [
                    'from' => 10,
                    'to' => 15,
                ],
                'value' => 2,
            ],
            [
                'condition' => [
                    'from' => 15,
                    'to' => 20,
                ],
                'value' => 3,
            ],
            [
                'condition' => [
                    'from' => 20,
                    'to' => 25,
                ],
                'value' => 4,
            ],
            [
                'condition' => [
                    'from' => 25,
                ],
                'value' => 5,
            ],
        ],
        'renewable' => [
            [
                'condition' => [
                    'to' => 15,
                ],
                'value' => 1,
            ],
            [
                'condition' => [
                    'from' => 15,
                    'to' => 30,
                ],
                'value' => 2,
            ],
            [
                'condition' => [
                    'from' => 30,
                    'to' => 45,
                ],
                'value' => 3,
            ],
            [
                'condition' => [
                    'from' => 45,
                    'to' => 60,
                ],
                'value' => 4,
            ],
            [
                'condition' => [
                    'from' => 60,
                ],
                'value' => 5,
            ],
        ],
        'investment' => [
            [
                'condition' => [
                    'to' => 0.5,
                ],
                'value' => 1,
            ],
            [
                'condition' => [
                    'from' => 0.5,
                    'to' => 2.5,
                ],
                'value' => 2,
            ],
            [
                'condition' => [
                    'from' => 2.5,
                    'to' => 4.5,
                ],
                'value' => 3,
            ],
            [
                'condition' => [
                    'from' => 4.5,
                    'to' => 6.5,
                ],
                'value' => 4,
            ],
            [
                'condition' => [
                    'from' => 6.5,
                ],
                'value' => 5,
            ],
        ],
    ];

    public function mount()
    {
        $this->building = HoomdossierSession::getBuilding(true);
        $this->masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
        $this->currentInputSource = HoomdossierSession::getInputSource(true);

        $advices = UserActionPlanAdvice::forInputSource($this->masterInputSource)
            ->where('user_id', $this->building->user->id)
            ->get();

        foreach (UserActionPlanAdviceService::getCategories() as $category) {
            foreach ($advices->where('category', $category) as $advice) {
                $advisable = $advice->userActionPlanAdvisable;
                if ($advice->user_action_plan_advisable_type === MeasureApplication::class) {
                    $this->cards[$category][$advice->id] = [
                        'name' => Str::limit($advisable->measure_name, 22),
                        'icon' => $advisable->configurations['icon'] ?? 'icon-tools',
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
        // Comfort logic
        // TODO: Calculations
        // TEMPORARY RANDOM CALC
        $percentage = mt_rand(0, 26);
        $this->evaluateCalculationResult('comfort', $percentage);

        // Renewable logic
        // TODO: Calculations
        // TEMPORARY RANDOM CALC
        $percentage = mt_rand(0, 100);
        $this->evaluateCalculationResult('renewable', $percentage);

        // Investment logic
        // TODO: Calculations
        // TEMPORARY RANDOM CALC
        $percentage = mt_rand(0, 10);
        $this->evaluateCalculationResult('investment', $percentage);


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

        $this->expectedInvestment = ($maxInvestment + $minInvestment) / 2;
        $this->yearlySavings = $savings;
        $this->availableSubsidy = $subsidy;
    }

    public function evaluateCalculationResult($field, $calculation)
    {
        // TODO: This will most likely come from the database at one point
        $calculationConditions = $this->calculationMap[$field];
        $value = 0;
        foreach ($calculationConditions as $calculationCondition) {
            $condition = $calculationCondition['condition'];
            // Upper range only
            if (empty($condition['from']) && ! empty($condition['to'])) {
                if ($calculation < $condition['to']) {
                    $value = $calculationCondition['value'];
                    break;
                }
            } // Full range
            elseif (! empty($condition['from']) && ! empty($condition['to'])) {
                if ($calculation >= $condition['from'] && $calculation < $condition['to']) {
                    $value = $calculationCondition['value'];
                    break;
                }
            } // Bottom range only
            elseif (! empty($condition['from']) && empty($condition['to'])) {
                if ($calculation >= $condition['from']) {
                    $value = $calculationCondition['value'];
                    break;
                }
            }
        }

        $this->{$field} = $value;
        // TODO: Deprecate this dispatch in Livewire V2 (IF POSSIBLE)
        $this->dispatchBrowserEvent('element:updated', ['field' => $field, 'value' => $value]);
    }
}
