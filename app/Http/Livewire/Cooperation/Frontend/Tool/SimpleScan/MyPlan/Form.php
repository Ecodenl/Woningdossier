<?php

namespace App\Http\Livewire\Cooperation\Frontend\Tool\SimpleScan\MyPlan;

use App\Helpers\Calculation\BankInterestCalculator;
use App\Helpers\HoomdossierSession;
use App\Helpers\Kengetallen;
use App\Helpers\Models\CooperationMeasureApplicationHelper;
use App\Helpers\NumberFormatter;
use App\Http\Livewire\Cooperation\Frontend\Tool\SimpleScan\CustomMeasureForm;
use App\Jobs\RefreshRegulationsForUserActionPlanAdvice;
use App\Models\Building;
use App\Models\CustomMeasureApplication;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\Scan;
use App\Models\UserActionPlanAdvice;
use App\Models\UserEnergyHabit;
use App\Scopes\VisibleScope;
use App\Services\Models\NotificationService;
use App\Services\Models\UserCostService;
use App\Services\UserActionPlanAdviceService;
use Illuminate\Support\Collection;
use App\Helpers\Arr;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Str;

class Form extends CustomMeasureForm
{
    use AuthorizesRequests;

    public array $cards = [
        UserActionPlanAdviceService::CATEGORY_COMPLETE => [],
        UserActionPlanAdviceService::CATEGORY_TO_DO => [],
        UserActionPlanAdviceService::CATEGORY_LATER => [],
    ];

    public array $hiddenCards = [
        UserActionPlanAdviceService::CATEGORY_COMPLETE => [],
        UserActionPlanAdviceService::CATEGORY_TO_DO => [],
        UserActionPlanAdviceService::CATEGORY_LATER => [],
    ];

    public InputSource $residentInputSource;
    public InputSource $coachInputSource;

    public Scan $scan;

    // Details
    public float $expectedInvestment = 0;
    public float $yearlySavings = 0;
    public float $availableSubsidy = 0;

    // Sliders
    public int $comfort = 0;
    public int $renewable = 0;
    public int $investment = 0;

    // Notifications
    public array $notifications = [];

    public function mount(Building $building)
    {
        $this->build($building);
        
        $this->residentInputSource = $this->currentInputSource->short === InputSource::RESIDENT_SHORT ? $this->currentInputSource : InputSource::findByShort(InputSource::RESIDENT_SHORT);
        $this->coachInputSource = $this->currentInputSource->short === InputSource::COACH_SHORT ? $this->currentInputSource : InputSource::findByShort(InputSource::COACH_SHORT);

        // Set cards
        $this->loadCustomMeasures();
        $this->loadVisibleCards();
        $this->loadHiddenCards();
        $this->recalculate();
    }

    public function render()
    {
        return view('livewire.cooperation.frontend.tool.simple-scan.my-plan.form');
    }

    public function save(int $index)
    {
        $customMeasureApplication = $this->submit($index, false);
        $this->loadCustomMeasures();

        $from = $customMeasureApplication->getSibling($this->masterInputSource);

        // All cards are shown by master source. We will fetch the master, as we need it for the card.
        $masterAdvice = $from->userActionPlanAdvices()->forInputSource($this->masterInputSource)->first();

        // Normally we would dispatch an event. However, the user "wants" the update to be real time, so we dispatch
        // the update manually so we can keep track.
        $this->dispatchRegulationUpdate($masterAdvice);
        $this->reload($masterAdvice);

        $this->dispatchBrowserEvent('close-modal');

        $this->recalculate();
    }

    public function cardMoved(string $fromCategory, string $toCategory, string $id, string $newOrder)
    {
        abort_if(HoomdossierSession::isUserObserving(), 403);

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

        $this->dispatchBrowserEvent('moved-card');
        $this->refreshAlerts();
    }

    public function cardTrashed(string $fromCategory, string $id)
    {
        abort_if(HoomdossierSession::isUserObserving(), 403);

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

        $this->dispatchBrowserEvent('trashed-card');
        $this->refreshAlerts();
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

        $investmentPerCard = [];

        foreach ($this->cards[UserActionPlanAdviceService::CATEGORY_TO_DO] as $order => $card) {
            $from = $card['costs']['from'] ?? 0;
            $to = $card['costs']['to'] ?? 0;

            if ($from <= 0 && $to > 0) {
                $cardInvestment = $to;
            } elseif ($to <= 0 && $from > 0) {
                $cardInvestment = $from;
            } elseif ($from > 0 && $to > 0) {
                $cardInvestment = (($from + $to) / 2);
            } else {
                // Force to 0, else, if not set, the next loop iteration will use the same value
                // from last loop, causing incorrect calculations
                $cardInvestment = 0;
            }

            $investmentPerCard[$order] = $cardInvestment;
            $investment += $cardInvestment;
            $savings += $card['savings'] ?? 0;

//            if ($card['subsidy'] === $this->SUBSIDY_AVAILABLE) {
//                $subsidy += ($to - $from) * $subsidyPercentage;
//            }
        }

        $investment = NumberFormatter::round($investment);
        $savings = NumberFormatter::round($savings);
        $subsidy = NumberFormatter::round($subsidy);

        $this->expectedInvestment = $investment;
        $this->yearlySavings = $savings;
        $this->availableSubsidy = $subsidy;

        $investmentRating = 0;

        // Now we have calculated the total investment, we need to calculate the investment rendement per card
        foreach ($this->cards[UserActionPlanAdviceService::CATEGORY_TO_DO] as $order => $card) {
            $cardInvestment = $investmentPerCard[$order];
            // Calculate the weight of this card
            // the card and investment could both be 0, so we use the highest investment amount and default it to 1 to prevent divisions by zero.
            $weight = $cardInvestment / max(1, $investment);

            // Calculate the interest just like in the expert tool
            $interest = BankInterestCalculator::getComparableInterest($cardInvestment, $card['savings']);

            // Get a rating based on interest, then multiply it by its weight, and then add it to the total
            $rating = $this->evaluateCalculationResult('investment', $interest, false);
            $investmentRating += ($weight * $rating);
        }

        // Set the investment value after rounding
        $investmentRating = round($investmentRating);
        $this->setField('investment', $investmentRating);

        // ---------------------------------------------------------------------
        // sustainability
        // ---------------------------------------------------------------------
        $package = $this->cards[UserActionPlanAdviceService::CATEGORY_TO_DO];
        $package = array_merge($package, $this->cards[UserActionPlanAdviceService::CATEGORY_COMPLETE]);
        $advices = UserActionPlanAdvice::forInputSource($this->masterInputSource)
            ->whereIn('id', \Illuminate\Support\Arr::pluck($package, 'id'))
            ->get();
        $totalGasSavings = $advices->sum('savings_gas');
        $totalElectricitySavings = $advices->sum('savings_electricity');

        $habit = $this->building->user
            ->energyHabit()
            ->forInputSource($this->masterInputSource)
            ->first();

        $usageGas = null;
        $usageElectricity = null;

        if ($habit instanceof UserEnergyHabit) {
            $usageGas = $habit->amount_gas;
            $usageElectricity = $habit->amount_electricity;
        }

        // calculate to kg. (set gas and electricity to same unit)
        $co2Reductions = $totalGasSavings * Kengetallen::CO2_SAVING_GAS +
            $totalElectricitySavings * Kengetallen::CO2_SAVINGS_ELECTRICITY;

        $co2Current = $usageGas * Kengetallen::CO2_SAVING_GAS +
            $usageElectricity * Kengetallen::CO2_SAVINGS_ELECTRICITY;

        // To calculate the new percentage, we need the new situation
        $co2New = $co2Current - $co2Reductions;

        // percentage = (new - old) / current * -1 * 100
        // * -1, because if it's a reduction, we will get a negative value
        // just ensure $co2Current is min. 1
        $renewablePercentage = ($co2New - $co2Current) / max(1, $co2Current) * -1 * 100;
        $this->evaluateCalculationResult('renewable', $renewablePercentage);

        // ---------------------------------------------------------------------
        // comfort
        // ---------------------------------------------------------------------
        $completeComfortCards = Arr::where($this->cards[UserActionPlanAdviceService::CATEGORY_COMPLETE],
            function ($value, $key) {
                return isset($value['comfort']);
            }
        );
        $completeComfort = array_sum(Arr::pluck($completeComfortCards, 'comfort'));

        $toDoComfortCards = Arr::where($this->cards[UserActionPlanAdviceService::CATEGORY_TO_DO],
            function ($value, $key) {
                return isset($value['comfort']);
            }
        );
        $toDoComfort = array_sum(Arr::pluck($toDoComfortCards, 'comfort'));

        $totalComfort = $completeComfort + $toDoComfort;
        $this->evaluateCalculationResult('comfort', $totalComfort);
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

        $this->authorize('view', $advice);

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

    public function addHiddenCardToBoard(string $category, string $id)
    {
        abort_if(HoomdossierSession::isUserObserving(), 403);

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

        $this->dispatchBrowserEvent('readded-card');
        $this->refreshAlerts();
    }

    public function checkNotifications()
    {
        $notificationService = NotificationService::init()
            ->forBuilding($this->building)
            ->forInputSource($this->masterInputSource);

        foreach ($this->notifications as $index => $notification) {
            if ($notificationService->setType($notification['type'])->setUuid($notification['uuid'])->isNotActive()) {
                unset($this->notifications[$index]);

                if (! empty($notification['action'])) {
                    $parameters = $notification['action']['parameters'] ?? [];
                    $this->{$notification['action']['method']}(...$parameters);
                }
            }
        }
    }

    /**
     * Reload the data of an advice.
     *
     * @param $advice
     *
     * @return void
     */
    public function reload($advice)
    {
        if (! $advice instanceof UserActionPlanAdvice) {
            $advice = UserActionPlanAdvice::allInputSources()->withInvisible()->find($advice);
        }

        if ($advice instanceof UserActionPlanAdvice) {
            $card = Arr::first($this->convertAdvicesToCards(collect([$advice]), $advice->category)[$advice->category]);

            $prop = $advice->visible ? 'cards' : 'hiddenCards';
            $cardData = Arr::where($this->{$prop}[$advice->category], function ($card, $order) use ($advice) {
                return $card['id'] == $advice->id;
            });

            if (empty($cardData)) {
                $newOrder = array_key_last($this->{$prop}[$advice->category]) ?? -1 + 1;
                $this->{$prop}[$advice->category][$newOrder] = $card;
            } else {
                $order = array_key_first($cardData);
                $this->{$prop}[$advice->category][$order] = $card;
            }
        }
    }

    public function evaluateCalculationResult(string $field, $calculation, bool $setValue = true)
    {
        // TODO: This will most likely come from the database at one point
        $calculationConditions = $this->calculationMap[$field];
        $value = 0;
        // TODO: Can we use the evaluator for this?
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

        if ($setValue) {
            $this->setField($field, $value);
        }

        return $value;
    }

    protected function refreshAlerts()
    {
        $this->emitTo('cooperation.frontend.layouts.parts.alerts', 'refreshAlerts');
    }

    private function loadVisibleCards()
    {
        foreach (UserActionPlanAdviceService::getCategories() as $category) {
            $advices = UserActionPlanAdvice::forInputSource($this->masterInputSource)
                ->where('user_id', $this->building->user->id)
                ->cooperationMeasureForType(
                    CooperationMeasureApplicationHelper::SMALL_MEASURE,
                    $this->masterInputSource
                )
                ->category($category)
                ->orderBy('order')
                ->get();

            $this->cards = array_merge($this->cards, $this->convertAdvicesToCards($advices, $category));
        }
    }

    private function loadHiddenCards()
    {
        foreach (UserActionPlanAdviceService::getCategories() as $category) {
            $hiddenAdvices = UserActionPlanAdvice::forInputSource($this->masterInputSource)
                ->invisible()
                ->cooperationMeasureForType(
                    CooperationMeasureApplicationHelper::SMALL_MEASURE,
                    $this->masterInputSource
                )
                ->where('user_id', $this->building->user->id)
                ->category($category)
                ->orderBy('order')
                ->get();

            $this->hiddenCards = array_merge(
                $this->hiddenCards,
                $this->convertAdvicesToCards($hiddenAdvices, $category)
            );
        }
    }

    private function convertAdvicesToCards(Collection $advices, string $category): array
    {
        $cards = [];
        $userCostService = UserCostService::init($this->building->user, $this->currentInputSource);

        // Order in the DB could have gaps or duplicates. For safe use, we set the order ourselves
        $order = 0;

        $hasUserCosts = false;

        foreach ($advices as $advice) {
            $advisable = $advice->userActionPlanAdvisable;

            if ($advice->user_action_plan_advisable_type === MeasureApplication::class) {
                // We only want expert scan steps to be linkable
                $route = null;
                if ($advisable->step->scan->short === Scan::EXPERT) {
                    $route = route('cooperation.frontend.tool.expert-scan.index', ['step' => $advisable->step]);
                }

                $cards[$category][$order] = [
                    'name' => Str::limit($advisable->measure_name, 57),
                    'icon' => $advisable->configurations['icon'] ?? 'icon-tools',
                    'info' => nl2br($advisable->measure_info),
                    'route' => $route,
                    'comfort' => $advisable->configurations['comfort'] ?? 0,
                ];

                // If the advisable has no tool questions (most likely maintenance measure) then it's empty and so the
                // user for certain doesn't have any costs. Else, it will get the answers, and if not viewable, then
                // the answer will be null. It will also be null if the user didn't fill it in. So, if all answers
                // are set, we can guarantee that this has user costs.
                $userCosts = $userCostService->forAdvisable($advisable)->getAnswers()[$advisable->id] ?? [];
                $hasUserCosts = ! empty($userCosts);
                foreach ($userCosts as $tqShort => $answer) {
                    if (is_null($answer)) {
                        $hasUserCosts = false;
                        break;
                    }
                }
            } else {
                // Custom measure has input source so we must fetch the advisable from the master input source
                if ($advice->user_action_plan_advisable_type === CustomMeasureApplication::class) {
                    $advisable = $advice->userActionPlanAdvisable()
                        ->forInputSource($this->masterInputSource)
                        ->first();

                    $index = array_key_first(Arr::where($this->customMeasureApplicationsFormData, function ($measure) use ($advisable) {
                        return $measure['id'] === $advisable->id;
                    }));
                }

                $cards[$category][$order] = [
                    'name' => Str::limit($advisable->name, 57),
                    'icon' => $advisable->extra['icon'] ?? 'icon-tools',
                    'info' => nl2br($advisable->info),
                ];

                if (isset($index)) {
                    $cards[$category][$order]['index'] = $index;
                }
            }

            $cards[$category][$order]['has_user_costs'] = $hasUserCosts;
            $cards[$category][$order]['subsidy_available'] = $advice->subsidy_available;
            $cards[$category][$order]['loan_available'] = $advice->loan_available;

            $cards[$category][$order]['id'] = $advice->id;
            $cards[$category][$order]['costs'] = [
                'from' => empty($advice->costs['from']) ? null : NumberFormatter::round($advice->costs['from']),
                'to' => empty($advice->costs['to']) ? null : NumberFormatter::round($advice->costs['to']),
            ];
            $cards[$category][$order]['savings'] = NumberFormatter::round($advice->savings_money ?? 0);

            ++$order;
        }

        return $cards;
    }

    private function setField($field, $value)
    {
        $this->{$field} = $value;
    }

    private function dispatchRegulationUpdate(UserActionPlanAdvice $advice)
    {
        $job = new RefreshRegulationsForUserActionPlanAdvice($advice);

        NotificationService::init()
            ->forBuilding($this->building)
            ->forInputSource($this->masterInputSource)
            ->setType(RefreshRegulationsForUserActionPlanAdvice::class)
            ->setActive([$job->uuid]);

        dispatch($job);

        $this->notifications[] = [
            'type' => RefreshRegulationsForUserActionPlanAdvice::class,
            'uuid' => $job->uuid,
            'action' => [
                'method' => 'reload',
                'parameters' => [$advice->id],
            ]
        ];
    }

    private array $calculationMap = [
        'comfort' => [
            [
                'condition' => [
                    'to' => 5,
                ],
                'value' => 1,
            ],
            [
                'condition' => [
                    'from' => 5,
                    'to' => 10,
                ],
                'value' => 2,
            ],
            [
                'condition' => [
                    'from' => 10,
                    'to' => 15,
                ],
                'value' => 3,
            ],
            [
                'condition' => [
                    'from' => 15,
                    'to' => 20,
                ],
                'value' => 4,
            ],
            [
                'condition' => [
                    'from' => 20,
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
}
