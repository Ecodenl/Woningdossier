<?php

namespace App\Http\Livewire\Frontend;

use Livewire\Component;

class HousingPlan extends Component
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

    public function mount()
    {
        // TODO: Find out how to get these from backend data
        $this->cards = [
            $this->CATEGORY_COMPLETE => [
                [
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
                [
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
                [
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
                [
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
                [
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
                [
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
                [
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
                [
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
                [
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
                [
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
                [
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
    }

    public function render()
    {
        return view('livewire.frontend.housing-plan');
    }

    public function updated($field)
    {
        $this->validateOnly($field, $this->rules);
    }

    public function submit()
    {
        $measureData = $this->validate($this->rules)['new_measure'];

        // Append card
        $this->cards[$this->category][] = [
            'name' => $measureData['subject'],
            'icon' => 'icon-tools',
            'price' => $measureData['price'],
            'subsidy' => $this->SUBSIDY_UNKNOWN,
            'savings' => $measureData['price']['from'] + ($measureData['price']['to'] / 10),
        ];

        $this->dispatchBrowserEvent('close-modal');
    }

    public function setCategory($category)
    {
        $this->category = $category;
    }
}
