<?php

namespace App\Http\Livewire\Cooperation\Frontend\Tool\QuickScan\MyPlan;

use App\Helpers\HoomdossierSession;
use App\Services\UserActionPlanAdviceService;
use Illuminate\Support\Str;
use Livewire\Component;

class Form extends Component
{
    public array $cards = [
        'complete' => [

        ],
        'to-do' => [

        ],
        'later' => [

        ],
    ];

    public array $new_measure = [];
    public int $investment = 0;
    public int $yearlySavings = 0;
    public int $availableSubsidy = 0;

    public string $category = '';

    // TODO: Move this to a constant helper when this is retrieved from backend
    public string $SUBSIDY_AVAILABLE = 'available';
    public string $SUBSIDY_UNAVAILABLE = 'unavailable';
    public string $SUBSIDY_UNKNOWN = 'unknown';

    public string $CATEGORY_COMPLETE = 'complete';
    public string $CATEGORY_TO_DO = 'to-do';
    public string $CATEGORY_LATER = 'later';

    protected $rules = [
        'new_measure.subject' => 'required',
        'new_measure.price.from' => 'required|numeric|min:0',
        'new_measure.price.to' => 'required|numeric|gt:new_measure.price.from',
    ];

    protected $listeners = [
        'cardMoved' => 'cardMoved',
    ];

    public function mount()
    {
        // TODO: Find out how to get these from backend data

        $building = HoomdossierSession::getBuilding(true);

        $building->

        $this->cards = [
            $this->CATEGORY_COMPLETE => [
                Str::random() => [
                    'name' => 'Ventilatie (mechanisch)',
                    'icon' => 'icon-ventilation',
                    'price' => [
                        'from' => 500,
                        'to' => 700,
                    ],
                    'subsidy' => $this->SUBSIDY_AVAILABLE,
                    'savings' => 0,
                    'info' => 'Ventilatie helpt met het goed doorluchten van het huis.',
                ],
                Str::random() => [
                    'name' => 'Nieuwe keuken',
                    'icon' => 'icon-kitchen',
                    'price' => [
                        'from' => 5000,
                        'to' => 10000,
                    ],
                    'subsidy' => $this->SUBSIDY_UNAVAILABLE,
                    'savings' => 300,
                    'info' => 'Een nieuwe keuken geeft jouw huis een betere uitstraling.',
                ],
                Str::random() => [
                    'name' => 'Nieuwe badkamer',
                    'icon' => 'icon-bathroom',
                    'price' => [
                        'from' => 5000,
                        'to' => 15000,
                    ],
                    'subsidy' => $this->SUBSIDY_UNAVAILABLE,
                    'savings' => 5000,
                    'info' => 'Een goede badkamer bespaart water.',
                ],
                Str::random() => [
                    'name' => 'Dakkapel',
                    'icon' => 'icon-dormer',
                    'price' => [
                        'from' => 8000,
                        'to' => 18000,
                    ],
                    'subsidy' => $this->SUBSIDY_AVAILABLE,
                    'savings' => 0,
                    'info' => 'Een dakkapel zorgt voor veel licht inval.',
                ],
            ],
            $this->CATEGORY_TO_DO => [
                Str::random() => [
                    'name' => 'Vloerverwarming',
                    'icon' => 'icon-radiant-floor-heating',
                    'price' => [
                        'from' => 1200,
                        'to' => 1800,
                    ],
                    'subsidy' => $this->SUBSIDY_AVAILABLE,
                    'savings' => 0,
                    'info' => 'Vloerverwaming houdt warmte vast.',
                ],
                Str::random() => [
                    'name' => 'Kozijnen vervangen',
                    'icon' => 'icon-window-frame',
                    'price' => [
                        'from' => 2500,
                        'to' => 2800,
                    ],
                    'subsidy' => $this->SUBSIDY_UNAVAILABLE,
                    'savings' => 1400,
                    'info' => 'Een nieuw kozijn kan helpen met isolatie.',
                ],
                Str::random() => [
                    'name' => 'Gevelisolatie',
                    'icon' => 'icon-wall-insulation-excellent',
                    'price' => [
                        'from' => 2500,
                        'to' => 2800,
                    ],
                    'subsidy' => $this->SUBSIDY_AVAILABLE,
                    'savings' => 0,
                    'info' => 'Goede isolatie, het spreekt voor zich.',
                ],
                Str::random() => [
                    'name' => 'Schilderwerk',
                    'icon' => 'icon-paint-job',
                    'price' => [
                        'from' => null,
                        'to' => null,
                    ],
                    'subsidy' => $this->SUBSIDY_UNAVAILABLE,
                    'savings' => 1000,
                    'info' => 'De kosten hangen af van het aantal manuren.',
                ],
                Str::random() => [
                    'name' => 'Vloerisolatie',
                    'icon' => 'icon-floor-insulation-excellent',
                    'price' => [
                        'from' => 1500,
                        'to' => 2000,
                    ],
                    'subsidy' => $this->SUBSIDY_AVAILABLE,
                    'savings' => 0,
                    'info' => 'Goede isolatie, het spreekt voor zich.',
                ],
            ],
            $this->CATEGORY_LATER => [
                Str::random() => [
                    'name' => 'Dakisolatie',
                    'icon' => 'icon-roof-insulation-excellent',
                    'price' => [
                        'from' => 700,
                        'to' => 900,
                    ],
                    'subsidy' => $this->SUBSIDY_AVAILABLE,
                    'savings' => 0,
                    'info' => 'Goede isolatie, het spreekt voor zich.',
                ],
                Str::random() => [
                    'name' => 'Isolerende beglazing',
                    'icon' => 'icon-glass-hr-p',
                    'price' => [
                        'from' => 700,
                        'to' => 3000,
                    ],
                    'subsidy' => $this->SUBSIDY_UNAVAILABLE,
                    'savings' => 900,
                    'info' => 'Goede isolatie, het spreekt voor zich.',
                ],
            ],
        ];

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
            'savings' => $measureData['price']['from'] + ($measureData['price']['to'] / 10),
        ];

        $this->dispatchBrowserEvent('close-modal');

        $this->recalculate();
    }

    public function setCategory($category)
    {
        $this->category = $category;
    }

    public function cardMoved($fromCategory, $toCategory, $id)
    {
        $card = $this->cards[$fromCategory][$id] ?? null;
        unset($this->cards[$fromCategory][$id]);

        if (! empty($card)) {
            $this->cards[$toCategory][$id] = $card;
        }

        $this->recalculate();
    }

    public function recalculate()
    {
        // TODO: Get logic for this. This is a guessed placeholder
        $subsidyPercentage = 0.1;

        $minInvestment = 0;
        $maxInvestment = 0;
        $savings = 0;
        $subsidy = 0;

        foreach ($this->cards[$this->CATEGORY_TO_DO] as $card) {
            $from = $card['price']['from'] ?? 0;
            $to = $card['price']['to'] ?? 0;

            $minInvestment += $from;
            $maxInvestment += $to;
            $savings += $card['savings'] ?? 0;

            if ($card['subsidy'] === $this->SUBSIDY_AVAILABLE) {
                $subsidy += ($to - $from) * $subsidyPercentage;
            }
        }

        $this->investment = ($maxInvestment + $minInvestment) / 2;
        $this->yearlySavings = $savings;
        $this->availableSubsidy = $subsidy;
    }
}
