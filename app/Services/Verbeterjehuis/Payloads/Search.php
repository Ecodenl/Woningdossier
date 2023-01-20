<?php

namespace App\Services\Verbeterjehuis\Payloads;

use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\ToolQuestion;
use App\Traits\FluentCaller;
use Illuminate\Support\Collection;

class Search implements VerbeterjehuisPayload
{
    use FluentCaller;

    public Collection $payload;

    public function __construct(array $payload)
    {
        $this->payload = collect($payload);
    }

    public function where()
    {
        $x = [
            0 => [
                'tags' => [
                    [
                        'value' => 'pils',
                        'id' => 74
                    ],
                    [
                        'value' => 'bier',
                        'id' => 69
                    ]
                ]
            ],
            1 => [
                'tags' => [
                    [
                        'value' => 'wodka',
                        'id' => 1
                    ],
                    [
                        'value' => 'whiskie',
                        'id' => 12
                    ]
                ]
            ],
        ];
        return $this->payload->where('Tags', 2835);

    }

}