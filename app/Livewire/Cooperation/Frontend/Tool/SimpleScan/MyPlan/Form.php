<?php

namespace App\Livewire\Cooperation\Frontend\Tool\SimpleScan\MyPlan;

use App\Enums\AdviceSource;
use App\Models\Step;
use Illuminate\Support\Facades\Gate;
use App\Helpers\Calculation\BankInterestCalculator;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Helpers\Kengetallen;
use App\Helpers\Models\CooperationMeasureApplicationHelper;
use App\Helpers\NumberFormatter;
use App\Livewire\Cooperation\Frontend\Tool\SimpleScan\CustomMeasureForm;
use App\Jobs\RefreshRegulationsForUserActionPlanAdvice;
use App\Models\Building;
use App\Models\CustomMeasureApplication;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\Scan;
use App\Models\SmartTwinPackage;
use App\Models\UserActionPlanAdvice;
use App\Models\UserEnergyHabit;
use App\Scopes\VisibleScope;
use App\Services\Models\NotificationService;
use App\Services\Models\UserCostService;
use App\Services\ExecutedAdviceService;
use App\Services\SmartTwin\PackageResults;
use App\Services\SmartTwin\SharedWoonplan;
use App\Services\SmartTwin\SmartTwinHandOff;
use App\Services\SmartTwin\WoonplanCardRules;
use App\Services\SmartTwin\WoonplanLock;
use App\Services\UserActionPlanAdviceService;
use Illuminate\Support\Collection;
use App\Helpers\Arr;
use Illuminate\Support\Str;
use Illuminate\View\View;
use App\Helpers\MediaHelper;
use App\Rules\MaxFilenameLength;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Computed;
use Livewire\WithFileUploads;

class Form extends CustomMeasureForm
{
    use WithFileUploads;

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

    /**
     * The column the "+" that opened the own-measure form belongs to: "Nu aanpakken" or
     * "Later uitvoeren". Only offered once the woonplan is SmartTwin's.
     */
    public string $newMeasureCategory = UserActionPlanAdviceService::CATEGORY_TO_DO;

    // The "uitgevoerd" popup (woonplan design "Processen woonplan bewoner"); see ExecutedAdviceService.
    public ?int $executedAdviceId = null;
    public string $executedName = '';
    public string $executedAt = '';
    public ?string $executedDescription = null;
    public bool $executedAlready = false;
    /** @var array<int, mixed> Livewire's temporary uploads. */
    public array $executedDocuments = [];

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

    public function mount(Building $building): void
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

    public function render(): View
    {
        return view('livewire.cooperation.frontend.tool.simple-scan.my-plan.form');
    }

    public function save(int $index): void
    {
        $this->abortIfReadOnly();

        $customMeasureApplication = $this->submit($index, false);
        $this->loadCustomMeasures();

        $from = $customMeasureApplication->getSibling($this->masterInputSource);

        // All cards are shown by master source. We will fetch the master, as we need it for the card.
        $masterAdvice = $from->userActionPlanAdvices()->forInputSource($this->masterInputSource)->first();

        // Normally we would dispatch an event. However, the user "wants" the update to be real time, so we dispatch
        // the update manually so we can keep track.
        $this->dispatchRegulationUpdate($masterAdvice);
        $this->reload($masterAdvice);

        $this->dispatch('close-modal');
        // The next "+" says again where its measure goes.
        $this->newMeasureCategory = UserActionPlanAdviceService::CATEGORY_TO_DO;

        $this->recalculate();
    }

    public function cardMoved(string $fromCategory, string $toCategory, string $id, string $newOrder): void
    {
        $this->abortIfReadOnly();

        // A SmartTwin woonplan has its card rules (WoonplanCardRules). The browser already refuses
        // what is not allowed; this is what makes it stick.
        if ($this->smartTwinOwnsWoonplan) {
            $outcome = WoonplanCardRules::move($this->kindOfCard($id), $fromCategory, $toCategory);

            if (WoonplanCardRules::ALLOWED !== $outcome) {
                $this->reloadCards();

                if (WoonplanCardRules::EXECUTE === $outcome) {
                    // Into "In orde" means carried out, which asks for a date first.
                    $this->openExecuted((int) $id);
                }

                $this->dispatch('moved-card');

                return;
            }
        }

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

        $this->dispatch('moved-card');
        $this->refreshAlerts();
    }

    public function cardTrashed(string $fromCategory, string $id): void
    {
        $this->abortIfReadOnly();

        if ($this->smartTwinOwnsWoonplan && ! WoonplanCardRules::mayTrash($this->kindOfCard($id))) {
            $this->reloadCards();
            $this->dispatch('trashed-card');

            return;
        }

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

        $this->dispatch('trashed-card');
        $this->refreshAlerts();
    }

    public function recalculate(): void
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
            $interest = BankInterestCalculator::getComparableInterest($cardInvestment, $card['savings'] ?? 0);

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
        $co2Reductions = $totalGasSavings * Kengetallen::CO2_SAVING_GAS + $totalElectricitySavings * Kengetallen::CO2_SAVINGS_ELECTRICITY;

        $co2Current = $usageGas * Kengetallen::CO2_SAVING_GAS + $usageElectricity * Kengetallen::CO2_SAVINGS_ELECTRICITY;

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
        $completeComfortCards = Arr::where(
            $this->cards[UserActionPlanAdviceService::CATEGORY_COMPLETE],
            function ($value, $key) {
                return isset($value['comfort']);
            }
        );
        $completeComfort = array_sum(Arr::pluck($completeComfortCards, 'comfort'));

        $toDoComfortCards = Arr::where(
            $this->cards[UserActionPlanAdviceService::CATEGORY_TO_DO],
            function ($value, $key) {
                return isset($value['comfort']);
            }
        );
        $toDoComfort = array_sum(Arr::pluck($toDoComfortCards, 'comfort'));

        $totalComfort = $completeComfort + $toDoComfort;
        $this->evaluateCalculationResult('comfort', $totalComfort);
    }

    public function reorder($category): void
    {
        // Reorder for each card in the list. We don't need to check invisible items, so we don't have to check
        // any other cards
        foreach ($this->cards[$category] as $order => $card) {
            $this->updateAdvice($card['id'], ['order' => $order]);
        }
    }

    /**
     * Whether this woonplan went over to SmartTwin (Hoomdossier::smartTwinOwnsWoonplanFor()). Then
     * every action applies to resident, coach and master alike (SharedWoonplan), and the expert scan
     * is closed (App\Http\Middleware\EnsureExpertScanAvailable). Computed, so a reorder that touches
     * every card in a column asks once.
     */
    #[Computed]
    public function smartTwinOwnsWoonplan(): bool
    {
        return Hoomdossier::smartTwinOwnsWoonplanFor($this->building);
    }

    protected function categoryForNewMeasure(): string
    {
        $columns = [UserActionPlanAdviceService::CATEGORY_TO_DO, UserActionPlanAdviceService::CATEGORY_LATER];

        // Whatever the browser sends, an own measure only ever lands in one of these two.
        return $this->smartTwinOwnsWoonplan && in_array($this->newMeasureCategory, $columns, true)
            ? $this->newMeasureCategory
            : UserActionPlanAdviceService::CATEGORY_TO_DO;
    }

    /**
     * The measures of the package SmartTwin calculated, for the "Verbeterpakket" label: a package
     * card keeps it in every column.
     *
     * @return array<int, int>
     */
    #[Computed]
    public function packageMeasureIds(): array
    {
        $package = $this->building->smartTwinPackage()->first();

        return $package instanceof SmartTwinPackage ? array_map('intval', $package->measure_application_ids ?? []) : [];
    }

    /**
     * Whether the woonplan is locked for whoever is looking: a coach working on it in SmartTwin
     * locks it for the resident (WoonplanLock). Shown, but nothing on it can be changed.
     */
    #[Computed]
    public function lockedForMe(): bool
    {
        return ! app(WoonplanLock::class)
            ->stateFor($this->building, $this->currentInputSource, HoomdossierSession::isUserObserving())
            ->residentMayEdit();
    }

    /**
     * No changes from someone observing, nor from a resident whose woonplan a coach has locked. The
     * page leaves the buttons out; this is what makes it stick.
     */
    private function abortIfReadOnly(): void
    {
        abort_if(HoomdossierSession::isUserObserving() || $this->lockedForMe, 403);
    }

    /**
     * Opens the "uitgevoerd" popup for a card: from its button, or from dragging it into "In orde".
     * For a measure already carried out it shows what was recorded, to change or to take back.
     */
    public function openExecuted(int $id): void
    {
        $this->abortIfReadOnly();
        abort_unless(ExecutedAdviceService::isAvailableFor($this->building), 403);

        $advice = $this->woonplanAdvice($id);

        $this->resetErrorBag();
        $this->executedAdviceId = $advice->id;
        $advisable = $advice->userActionPlanAdvisable()->withoutGlobalScopes()->first();
        $this->executedName = (string) ($advisable instanceof MeasureApplication ? $advisable->measure_name : $advisable?->name);
        $this->executedAlready = $advice->isExecuted();
        $this->executedAt = ($advice->executed_at ?? now())->format('Y-m-d');
        $this->executedDescription = $advice->executed_description;
        $this->executedDocuments = [];

        $this->dispatch('open-executed-modal');
    }

    public function saveExecuted(ExecutedAdviceService $executed): void
    {
        $this->abortIfReadOnly();

        $this->validate([
            'executedAt'            => ['required', 'date', 'before_or_equal:today'],
            'executedDescription'   => ['nullable', 'string', 'max:5000'],
            // As the "Bestanden" page takes them; the documents end up there.
            'executedDocuments.*'   => ['file', 'mimes:' . MediaHelper::getAllMimes(), 'max:' . MediaHelper::getMaxFileSize(), new MaxFilenameLength()],
        ], [], [
            'executedAt'          => __('cooperation/frontend/tool.my-plan.executed.date'),
            'executedDescription' => __('cooperation/frontend/tool.my-plan.executed.description'),
            'executedDocuments.*' => __('cooperation/frontend/tool.my-plan.executed.documents'),
        ]);

        $advice = $this->woonplanAdvice((int) $this->executedAdviceId);
        $executed->markExecuted($advice, Carbon::parse($this->executedAt), $this->executedDescription);

        foreach ($this->executedDocuments as $document) {
            $executed->attachDocument(
                $advice,
                $document->getRealPath(),
                pathinfo($document->getClientOriginalName(), PATHINFO_FILENAME),
                $this->currentInputSource,
            );
            $document->delete();
        }

        $this->closeExecuted();
    }

    public function undoExecuted(ExecutedAdviceService $executed): void
    {
        $this->abortIfReadOnly();

        $executed->undoExecuted($this->woonplanAdvice((int) $this->executedAdviceId));

        $this->closeExecuted();
    }

    private function closeExecuted(): void
    {
        $this->executedAdviceId = null;
        $this->executedDocuments = [];
        $this->reloadCards();
        $this->dispatch('close-modal');
    }

    /**
     * An advice of this woonplan, by the id of its card.
     */
    private function woonplanAdvice(int $id): UserActionPlanAdvice
    {
        $advice = UserActionPlanAdvice::allInputSources()
            ->withoutGlobalScope(VisibleScope::class)
            ->where('user_id', $this->building->user->id)
            ->findOrFail($id);

        Gate::authorize('view', $advice);

        return $advice;
    }

    /**
     * "Gekozen pakket" under the columns, once the woonplan is SmartTwin's. Read from the cards as
     * they are now, so it follows every move.
     */
    #[Computed]
    public function packageResults(): ?PackageResults
    {
        if (! $this->smartTwinOwnsWoonplan) {
            return null;
        }

        $package = SmartTwinPackage::where('building_id', $this->building->id)->first();

        // The woonplan as it is shown: master, visible cards included or not, isIntactIn decides.
        $intact = $package instanceof SmartTwinPackage && $package->isIntactIn(
            UserActionPlanAdvice::withoutGlobalScopes()
                ->where('user_id', $this->building->user->id)
                ->where('input_source_id', $this->masterInputSource->id)
                ->get(),
        );

        return PackageResults::from($package, $intact, $this->cards[UserActionPlanAdviceService::CATEGORY_TO_DO] ?? []);
    }

    /**
     * Whether "Maatregelpakket aanpassen" may send this account to SmartTwin.
     */
    #[Computed]
    public function canHandOff(): bool
    {
        // Not while a coach has the plan locked: no new calculation until the coach is done.
        return ! $this->lockedForMe && SmartTwinHandOff::isOfferedFor($this->building);
    }

    private function kindOfCard(string $id): string
    {
        $advice = UserActionPlanAdvice::allInputSources()
            ->withoutGlobalScope(VisibleScope::class)
            ->where('user_id', $this->building->user->id)
            ->findOrFail($id);

        return WoonplanCardRules::kindOf($advice, $this->packageMeasureIds);
    }

    /**
     * Puts the cards back as they are stored, after the browser moved one where it may not go.
     */
    private function reloadCards(): void
    {
        foreach (UserActionPlanAdviceService::getCategories() as $category) {
            $this->cards[$category] = [];
            $this->hiddenCards[$category] = [];
        }

        $this->loadVisibleCards();
        $this->loadHiddenCards();
        $this->recalculate();
    }

    #[Computed]
    public function expertSteps(): Collection
    {
        $expertScan = Scan::expert();
        return Step::forScan($expertScan)
            ->whereNotIn('short', ['high-efficiency-boiler', 'heater', 'heat-pump'])
            ->get();
    }

    public function updateAdvice($id, array $update): void
    {
        $this->abortIfReadOnly();

        // Get moved advice (will be for master input source)
        $advice = UserActionPlanAdvice::allInputSources()
            ->withoutGlobalScope(VisibleScope::class)
            ->find($id);

        Gate::authorize('view', $advice);

        // One woonplan for everyone: master, which is what is shown and what $advice is, then the
        // resident's and the coach's rows after it.
        if ($this->smartTwinOwnsWoonplan) {
            $advice->update($update);
            app(SharedWoonplan::class)->copyFromMaster($advice);

            return;
        }

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

    public function addHiddenCardToBoard(string $category, string $id): void
    {
        $this->abortIfReadOnly();

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

        $this->dispatch('readded-card');
        $this->refreshAlerts();
    }

    public function checkNotifications(): void
    {
        $notificationService = NotificationService::init()
            ->forBuilding($this->building);

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
     */
    public function reload(int|UserActionPlanAdvice $advice): void
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
                $newOrder = (array_key_last($this->{$prop}[$advice->category]) ?? -1) + 1;
                $this->{$prop}[$advice->category][$newOrder] = $card;
            } else {
                $order = array_key_first($cardData);
                $this->{$prop}[$advice->category][$order] = $card;
            }
        }
    }

    public function evaluateCalculationResult(string $field, float $calculation, bool $setValue = true): int
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
            } elseif (! empty($condition['from']) && ! empty($condition['to'])) {
                // Full range
                if ($calculation >= $condition['from'] && $calculation < $condition['to']) {
                    $value = $calculationCondition['value'];
                    break;
                }
            } elseif (! empty($condition['from']) && empty($condition['to'])) {
                // Bottom range only
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

    protected function refreshAlerts(): void
    {
        $this->dispatch('refreshAlerts')->to('cooperation.frontend.layouts.parts.alerts');
    }

    private function loadVisibleCards(): void
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

    private function loadHiddenCards(): void
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
        $userCostService = UserCostService::init()->user($this->building->user)->inputSource($this->currentInputSource);

        // Order in the DB could have gaps or duplicates. For safe use, we set the order ourselves
        $order = 0;

        $hasUserCosts = false;

        foreach ($advices as $advice) {
            $advisable = $advice->userActionPlanAdvisable;

            if ($advice->user_action_plan_advisable_type === MeasureApplication::class) {
                // We only want expert scan steps to be linkable
                $route = null;
                if ($advisable->step->scan->short === Scan::EXPERT) {
                    // Pass the cooperation explicitly: Livewire update requests skip the cooperation
                    // middleware, so its URL default is not set when the cards are reloaded.
                    $route = route('cooperation.frontend.tool.expert-scan.index', [
                        'cooperation' => $this->building->user->cooperation,
                        'step'        => $advisable->step,
                    ]);
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
                // For added clarification: has user costs means that subsidy was deducted from the own total, meaning
                // both own_total and subsidy_total need to be filled in.
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
            // SmartTwin gives the subsidy for the package as a whole; once the woonplan is SmartTwin's,
            // the Verbeterjehuis subsidy per measure is not shown. Loans wait for the stuurgroep (F1c).
            $cards[$category][$order]['subsidy_available'] = ! $this->smartTwinOwnsWoonplan && $advice->subsidy_available;
            $cards[$category][$order]['loan_available'] = $advice->loan_available;

            $cards[$category][$order]['id'] = $advice->id;
            $cards[$category][$order]['executed_at'] = $advice->executed_at?->format('d-m-Y');
            // What SmartTwin told us about this advice, for the (i) of the card.
            $cards[$category][$order]['smart_twin'] = $this->smartTwinOwnsWoonplan ? $advice->smartTwinDetails()?->toArray() : null;

            // What the card may do; see WoonplanCardRules. Before the switch every card moves and goes
            // to the trash as it always did.
            $kind = $this->smartTwinOwnsWoonplan ? WoonplanCardRules::kindOf($advice, $this->packageMeasureIds) : null;
            $cards[$category][$order]['kind'] = $kind;
            $cards[$category][$order]['targets'] = is_null($kind)
                ? array_values(UserActionPlanAdviceService::getCategories())
                : WoonplanCardRules::targets($kind, $category);
            $cards[$category][$order]['trashable'] = is_null($kind) || WoonplanCardRules::mayTrash($kind);
            $cards[$category][$order]['in_package'] = ! is_null($kind)
                && $advice->user_action_plan_advisable_type === MeasureApplication::class
                && in_array((int) $advice->user_action_plan_advisable_id, $this->packageMeasureIds, true);
            // SmartTwin's prices are not rounded to steps of five, the woonplan's own still are; both
            // are shown in whole euros (Patrick, 15 October 2026). The lines under the columns show
            // these same amounts, so a card and its line never differ; see PackageResults.
            $fromSmartTwin = AdviceSource::SMART_TWIN === $advice->source;
            $cards[$category][$order]['costs'] = [
                'from' => empty($advice->costs['from']) ? null : ($fromSmartTwin ? (float) $advice->costs['from'] : NumberFormatter::round($advice->costs['from'])),
                'to' => empty($advice->costs['to']) ? null : ($fromSmartTwin ? (float) $advice->costs['to'] : NumberFormatter::round($advice->costs['to'])),
            ];
            // Null rather than zero when there is no saving: an advice that came from elsewhere may
            // carry a price without one, and "€ 0" reads to a resident as "this gains you nothing".
            // The card hides it; the sums below treat it as zero, because an unknown cannot be added.
            $cards[$category][$order]['savings'] = is_null($advice->savings_money)
                ? null
                : NumberFormatter::round($advice->savings_money);

            ++$order;
        }

        return $cards;
    }

    private function setField($field, $value): void
    {
        $this->{$field} = $value;
    }

    private function dispatchRegulationUpdate(UserActionPlanAdvice $advice): void
    {
        $job = new RefreshRegulationsForUserActionPlanAdvice($advice);

        NotificationService::init()
            ->forBuilding($this->building)
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
