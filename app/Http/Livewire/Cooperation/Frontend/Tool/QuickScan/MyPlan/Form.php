<?php

namespace App\Http\Livewire\Cooperation\Frontend\Tool\QuickScan\MyPlan;

use App\Helpers\HoomdossierSession;
use App\Helpers\Kengetallen;
use App\Helpers\NumberFormatter;
use App\Helpers\StepHelper;
use App\Models\Building;
use App\Models\CustomMeasureApplication;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\User;
use App\Models\UserActionPlanAdvice;
use App\Models\UserActionPlanAdviceComments;
use App\Scopes\VisibleScope;
use App\Services\UserActionPlanAdviceService;
use Illuminate\Database\Eloquent\Collection;
use App\Helpers\Arr;
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

    public array $hiddenCards = [
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
    public $residentInputSource;
    public $coachInputSource;

    public array $custom_measure_application = [];

    // Details
    public $expectedInvestment = 0;
    public $yearlySavings = 0;
    public $availableSubsidy = 0;

    // Sliders
    public $comfort = 0;
    public $renewable = 0;
    public $investment = 0;

    public string $category = '';

    /** @var null|UserActionPlanAdviceComments */
    public $residentComment;
    public string $residentCommentText = '';
    /** @var null|UserActionPlanAdviceComments */
    public $coachComment;
    public string $coachCommentText = '';

    // TODO: Move this to a constant helper when this is retrieved from backend
    public string $SUBSIDY_AVAILABLE = 'available';
    public string $SUBSIDY_UNAVAILABLE = 'unavailable';
    public string $SUBSIDY_UNKNOWN = 'unknown';

    protected $rules = [
        'custom_measure_application.name' => 'required',
        'custom_measure_application.info' => 'required',
        'custom_measure_application.costs.from' => 'required|numeric|min:0',
        'custom_measure_application.costs.to' => 'required|numeric|gt:custom_measure_application.costs.from',
        'custom_measure_application.savings_money' => 'nullable|numeric',
    ];

    protected $listeners = [
        'cardMoved', 'cardTrashed', 'addHiddenCardToBoard',
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

    public function mount(Building $building)
    {
        $this->building = $building;
        // Set needed input sources
        $this->masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
        $this->currentInputSource = HoomdossierSession::getInputSource(true);
        $this->residentInputSource = $this->currentInputSource->short === InputSource::RESIDENT_SHORT
            ? $this->currentInputSource
            : InputSource::findByShort(InputSource::RESIDENT_SHORT);
        $this->coachInputSource = $this->currentInputSource->short === InputSource::COACH_SHORT
            ? $this->currentInputSource
            : InputSource::findByShort(InputSource::COACH_SHORT);

        // Set comments
        $this->residentComment = UserActionPlanAdviceComments::forInputSource($this->residentInputSource)
            ->where('user_id', $this->building->user->id)->first();
        $this->residentCommentText = $this->residentComment instanceof UserActionPlanAdviceComments ? $this->residentComment->comment : '';

        $this->coachComment = UserActionPlanAdviceComments::forInputSource($this->coachInputSource)
            ->where('user_id', $this->building->user->id)->first();
        $this->coachCommentText = $this->coachComment instanceof UserActionPlanAdviceComments ? $this->coachComment->comment : '';

        // Set cards
        foreach (UserActionPlanAdviceService::getCategories() as $category) {
            $advices = UserActionPlanAdvice::forInputSource($this->masterInputSource)
                ->where('user_id', $this->building->user->id)
                ->where('category', $category)
                ->orderBy('order')
                ->get();

            $this->cards = array_merge($this->cards, $this->convertAdvicesToCards($advices, $category));
        }

        $this->loadHiddenCards();
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
        // Before we can validate, we must convert human format to proper format
        $costs = $this->custom_measure_application['costs'] ?? [];
        $costs['from'] = NumberFormatter::mathableFormat(str_replace('.', '', $costs['from'] ?? ''), 2);
        $costs['to'] = NumberFormatter::mathableFormat(str_replace('.', '', $costs['to'] ?? ''), 2);
        $this->custom_measure_application['costs'] = $costs;
        $this->custom_measure_application['savings_money'] = NumberFormatter::mathableFormat(str_replace('.', '', $this->custom_measure_application['savings_money'] ?? 0), 2);

        $measureData = $this->validate($this->rules)['custom_measure_application'];

        // Create custom measure
        $customMeasureApplication = CustomMeasureApplication::create([
            'building_id' => $this->building->id,
            'input_source_id' => $this->currentInputSource->id,
            'hash' => Str::uuid(),
            'name' => ['nl' => $measureData['name']],
            'info' => ['nl' => $measureData['info']],
        ]);

        // Get order based on current total (we don't have to add or subtract since count gives us the total, which
        // is equal to indexable order + 1)
        $order = count($this->cards[$this->category]);

        // Build user advice
        $advice = $customMeasureApplication
            ->userActionPlanAdvices()
            ->create(
                [
                    'user_id' => $this->building->user->id,
                    'input_source_id' => $this->currentInputSource->id,
                    'category' => $this->category,
                    'visible' => true,
                    'order' => $order,
                    'costs' => $measureData['costs'],
                    'savings_money' => $measureData['savings_money'] ?? 0,
                ],
            );

        // Append card
        $this->cards[$this->category][$order] = [
            'id' => $advice->id,
            'name' => $customMeasureApplication->name,
            'info' => $customMeasureApplication->info,
            'icon' => 'icon-tools',
            'costs' => $advice->costs,
            'subsidy' => $this->SUBSIDY_UNKNOWN,
            'savings' => $advice->savings_money ?? 0,
        ];

        $this->dispatchBrowserEvent('close-modal');
        // Reset the modal
        $this->custom_measure_application = [];

        $this->recalculate();
    }

    public function setCategory($category)
    {
        $this->category = $category;
    }

    public function cardMoved($fromCategory, $toCategory, $id, $newOrder)
    {
        // Disclaimer: We have to do it like this, because JavaScript re-sorts arrays / objects to given numeric
        // keys, so we must ENSURE the order is 100% valid from top to bottom

        // Get the original card object
        $cardData = Arr::where($this->cards[$fromCategory], function ($card, $order) use ($id) {
            return $card['id'] == $id;
        });

        // Structure: order => card
        if (! empty($cardData)) {
            $oldOrder = array_key_first($cardData);
            $movedCard = $cardData[$oldOrder];

            // Remove card from the original category
            unset($this->cards[$fromCategory][$oldOrder]);

            // Reorder the old category
            $oldCards = $this->cards[$fromCategory];
            $newCards = [];

            // Simple reorder: we just set values to the loop iteration (in index form), because the moved
            // card is already removed
            $loop = 0;
            foreach ($oldCards as $card) {
                $newCards[$loop] = $card;
                ++$loop;
            }
            $this->cards[$fromCategory] = $newCards;

            // Add moved card into new category
            $oldCards = $this->cards[$toCategory];
            $newCards = [];

            // If the new order is above the max order, we just append it
            if ($newOrder > count($oldCards) - 1) {
                $newCards = $oldCards;
                $newCards[$newOrder] = $movedCard;
            } else {
                // The logic here is simple but important to know:
                // We loop through the cards by indexable loop iteration. We check if that iteration is equal to the
                // new order. If it that's the case, the moved card must be inserted there, and the current card
                // must be placed one higher. If the iteration is above new order, they need to be placed one higher.
                // Otherwise, they can stay in their position.
                $loop = 0;
                foreach ($oldCards as $card) {
                    if ($loop == $newOrder) {
                        $newCards[$loop] = $movedCard;
                        $newCards[$loop + 1] = $card;
                    } elseif ($loop > $newOrder) {
                        $newCards[$loop + 1] = $card;
                    } else {
                        $newCards[$loop] = $card;
                    }
                    ++$loop;
                }
            }
            $this->cards[$toCategory] = $newCards;

            $this->updateAdvice($id, ['category' => $toCategory]);

            // Reorder in DB also
            $this->reorder($toCategory);
            if ($fromCategory !== $toCategory) {
                $this->reorder($fromCategory);
            }

            $this->recalculate();
        }
    }

    public function cardTrashed($fromCategory, $id)
    {
        // Get the original card object
        $cardData = Arr::where($this->cards[$fromCategory], function ($card, $order) use ($id) {
            return $card['id'] == $id;
        });

        if (! empty($cardData)) {
            $oldOrder = array_key_first($cardData);
            $trashedCard = $cardData[$oldOrder];

            // Remove card from the list
            unset($this->cards[$fromCategory][$oldOrder]);

            // Add card to hidden cards
            $this->hiddenCards[$fromCategory][] = $trashedCard;

            // Set invisible
            $this->updateAdvice($id, ['visible' => false]);

            $this->recalculate();
        }
    }

    public function recalculate()
    {
        // ---------------------------------------------------------------------
        // investment
        // ---------------------------------------------------------------------
        // TODO: Get logic for subsidy.
        $subsidyPercentage = 0.1;

        $investment = 0;
        $savings = 0;
        $subsidy = 0;

        foreach ($this->cards[UserActionPlanAdviceService::CATEGORY_TO_DO] as $card) {
            $from = $card['costs']['from'] ?? 0;
            $to = $card['costs']['to'] ?? 0;

            if ($from <= 0 && $to > 0) {
                $investment += $to;
            } elseif ($to <= 0 && $from > 0) {
                $investment += $from;
            } elseif ($from > 0 && $to > 0) {
                $investment += (($from + $to) / 2);
            }

            $savings += $card['savings'] ?? 0;

//            if ($card['subsidy'] === $this->SUBSIDY_AVAILABLE) {
//                $subsidy += ($to - $from) * $subsidyPercentage;
//            }
        }

        $investment = NumberFormatter::round($investment);
        $savings = NumberFormatter::round($savings);

        $investmentPercentage = (max(1, $savings) / $investment) * 100;
        $this->evaluateCalculationResult('investment', $investmentPercentage);

        // ---------------------------------------------------------------------
        // sustainability
        // ---------------------------------------------------------------------
        $package = $this->cards[UserActionPlanAdviceService::CATEGORY_COMPLETE];
        $package = array_merge($package, $this->cards[UserActionPlanAdviceService::CATEGORY_COMPLETE]);
        $advices = UserActionPlanAdvice::forInputSource($this->masterInputSource)
                                       ->whereIn('id', \Illuminate\Support\Arr::pluck($package, 'id'))
                                       ->get();
        $totalGasSavings = $advices->sum('savings_gas');
        $totalElectricitySavings = $advices->sum('savings_electricity');

        $habits = $this->building->user
            ->energyHabit()
            ->forInputSource($this->masterInputSource)
            ->get();
        $usageGas = $habits->get('amount_gas');
        $usageElectricity = $habits->get('amount_electricity');

        // calculate to kg. (set gas and electricity to same unit)
        $co2Reductions = ($totalGasSavings * Kengetallen::CO2_SAVING_GAS) +
                         ($totalElectricitySavings * Kengetallen::CO2_SAVINGS_ELECTRICITY);

        $co2Current = ($usageGas * Kengetallen::CO2_SAVING_GAS) +
                      ($usageElectricity * Kengetallen::CO2_SAVINGS_ELECTRICITY);

        // percentage = (reduction / current) * 100
        // just ensure $co2Current is min. 1
        // just ensure max percentage = 100.
        $renewablePercentage = min(($co2Reductions / max(1,$co2Current)) * 100,100);

        $this->evaluateCalculationResult('renewable', $renewablePercentage);

        // ---------------------------------------------------------------------
        // comfort
        // ---------------------------------------------------------------------
        // Comfort logic
        // TODO: Calculations
        // TEMPORARY RANDOM CALC
        $percentage = mt_rand(0, 100);
        $this->evaluateCalculationResult('comfort', $percentage);

        $this->expectedInvestment = $investment;
        $this->yearlySavings = $savings;
        $this->availableSubsidy = $subsidy;
    }

    public function reorder($category)
    {
        // Reorder for each card in the list. We don't need to check invisible items, so we don't have to check
        // any other cards
        foreach ($this->cards[$category] as $order => $card) {
            $this->updateAdvice($card['id'], ['order' => $order]);
        }
    }

    public function updateAdvice($id, array $update)
    {
        // Get moved advice (will be for master input source)
        $advice = UserActionPlanAdvice::allInputSources()
            ->withoutGlobalScope(VisibleScope::class)
            ->find($id);

        // If it's a custom measure, we need to get the sibling because the custom measure also has an input source
        if ($advice->user_action_plan_advisable_type === CustomMeasureApplication::class) {
            $advisable = $advice->userActionPlanAdvisable()->forInputSource($this->masterInputSource)->first();
            if ($advisable instanceof CustomMeasureApplication) {
                $advisableId = optional($advisable->getSibling($this->currentInputSource))->id;
            }
        } else {
            $advisableId = $advice->user_action_plan_advisable_id;
        }

        $myAdvice = null;
        if (! empty($advisableId)) {
            // Get MY advice
            $myAdvice = UserActionPlanAdvice::forInputSource($this->currentInputSource)
                ->where('user_id', $this->building->user->id)
                ->where('user_action_plan_advisable_type', $advice->user_action_plan_advisable_type)
                ->where('user_action_plan_advisable_id', $advisableId)
                ->where('step_id', $advice->step_id)
                ->first();
        }

        // If my advice exists, we update my advice, and the trait will handle the rest for the master input source
        if ($myAdvice instanceof UserActionPlanAdvice) {
            $myAdvice->update($update);
        } else {
            // Otherwise we will update master ourselves (advice could be from the coach if the user is a resident
            // or vice versa)
            $advice->update($update);
        }
    }

    public function saveComment(string $sourceShort)
    {
        if ($sourceShort === InputSource::RESIDENT_SHORT || $sourceShort === InputSource::COACH_SHORT) {
            $commentShort = "{$sourceShort}Comment";
            $commentText = $this->{"{$sourceShort}CommentText"};
            $inputSource = $this->{"{$sourceShort}InputSource"};

            if ($inputSource->short === $this->currentInputSource->short) {
                if ($this->{$commentShort} instanceof UserActionPlanAdviceComments) {
                    $this->{$commentShort}->update([
                        'comment' => $commentText,
                    ]);
                } else {
                    $this->{$commentShort} = UserActionPlanAdviceComments::create([
                        'user_id' => $this->building->user->id,
                        'input_source_id' => $inputSource->id,
                        'comment' => $commentText,
                    ]);
                }
            }
        }
    }

    public function addHiddenCardToBoard($category, $id)
    {
        $cardData = Arr::where($this->hiddenCards[$category], function ($card, $order) use ($id) {
            return $card['id'] == $id;
        });

        if (! empty($cardData)) {
            $oldOrder = array_key_first($cardData);
            $addedCard = $cardData[$oldOrder];

            // Remove card from the original category
            unset($this->hiddenCards[$category][$oldOrder]);

            // Add moved card into new category
            $cards = $this->cards[$category];
            // Append to end
            $cards[] = $addedCard;
            $newCards = [];
            $loop = 0;
            foreach ($cards as $card) {
                $newCards[$loop] = $card;
                ++$loop;
            }
            $this->cards[$category] = $newCards;

            // Set visible and order
            $this->updateAdvice($id, ['visible' => true]);
            $this->reorder($category);

            $this->recalculate();
        }
    }

    private function loadHiddenCards()
    {
        foreach (UserActionPlanAdviceService::getCategories() as $category) {
            $hiddenAdvices = UserActionPlanAdvice::forInputSource($this->masterInputSource)
                ->withoutGlobalScope(VisibleScope::class)
                ->where('user_id', $this->building->user->id)
                ->where('category', $category)
                ->where('visible', false)
                ->orderBy('order')
                ->get();

            $this->hiddenCards = array_merge($this->hiddenCards, $this->convertAdvicesToCards($hiddenAdvices, $category));
        }
    }

    private function convertAdvicesToCards(Collection $advices, string $category): array
    {
        $cards = [];

        // Order in the DB could have gaps or duplicates. For safe use, we set the order ourselves
        $order = 0;
        foreach ($advices as $advice) {
            $advisable = $advice->userActionPlanAdvisable;
            if ($advice->user_action_plan_advisable_type === MeasureApplication::class) {
                $cards[$category][$order] = [
                    'name' => Str::limit($advisable->measure_name, 22),
                    'icon' => $advisable->configurations['icon'] ?? 'icon-tools',
                    // TODO: Subsidy
                    'subsidy' => $this->SUBSIDY_AVAILABLE,
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

                $cards[$category][$order] = [
                    'name' => Str::limit($advisable->name, 22),
                    'icon' => $advisable->extra['icon'] ?? 'icon-tools',
                    // TODO: Subsidy
                    'subsidy' => $this->SUBSIDY_UNKNOWN,
                    'info' => $advisable->info,
                ];
            }

            $cards[$category][$order]['id'] = $advice->id;
            $cards[$category][$order]['costs'] = [
                'from' => empty($advice->costs['from']) ? null : NumberFormatter::round($advice->costs['from']),
                'to' =>  empty($advice->costs['to']) ? null : NumberFormatter::round($advice->costs['to']),
            ];
            $cards[$category][$order]['savings'] = NumberFormatter::round($advice->savings_money ?? 0);

            ++$order;
        }

        return $cards;
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
