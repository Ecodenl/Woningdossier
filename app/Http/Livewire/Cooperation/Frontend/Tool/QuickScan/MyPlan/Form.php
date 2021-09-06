<?php

namespace App\Http\Livewire\Cooperation\Frontend\Tool\QuickScan\MyPlan;

use App\Helpers\HoomdossierSession;
use App\Helpers\NumberFormatter;
use App\Helpers\StepHelper;
use App\Models\Building;
use App\Models\CustomMeasureApplication;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\UserActionPlanAdvice;
use App\Models\UserActionPlanAdviceComments;
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
    public $residentInputSource;
    public $coachInputSource;

    public array $custom_measure_application = [];
    public int $investment = 0;
    public int $yearlySavings = 0;
    public int $availableSubsidy = 0;

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

            // Order in the DB could have gaps or duplicates. For safe use, we set the order ourselves
            $order = 0;
            foreach ($advices as $advice) {
                $advisable = $advice->userActionPlanAdvisable;
                if ($advice->user_action_plan_advisable_type === MeasureApplication::class) {
                    $this->cards[$category][$order] = [
                        'name' => Str::limit($advisable->measure_name, 22),
                        'icon' => $this->iconMap[$advisable->short] ?? 'icon-tools',
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

                    $this->cards[$category][$order] = [
                        'name' => Str::limit($advisable->name, 22),
                        'icon' => $advisable->extra['icon'] ?? 'icon-tools',
                        // TODO: Subsidy
                        'subsidy' => $this->SUBSIDY_UNKNOWN,
                        'info' => $advisable->info,
                    ];
                }

                $this->cards[$category][$order]['id'] = $advice->id;
                $this->cards[$category][$order]['costs'] = [
                    'from' => $advice->costs['from'] ?? null,
                    'to' => $advice->costs['to'] ?? null,
                ];
                $this->cards[$category][$order]['savings'] = $advice->savings_money ?? 0;

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

    public function recalculate()
    {
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

        $this->investment = $investment;
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
                        'input_source_id' => $inputSource,
                        'comment' => $commentText,
                    ]);
                }
            }
        }
    }
}
