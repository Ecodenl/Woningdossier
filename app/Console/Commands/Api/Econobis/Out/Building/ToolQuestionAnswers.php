<?php

namespace App\Console\Commands\Api\Econobis\Out\Building;

use App\Models\Building;
use App\Models\InputSource;
use App\Models\ToolQuestion;
use App\Services\Econobis\Api\Client;
use App\Services\Econobis\Api\Econobis;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class ToolQuestionAnswers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:econobis:out:building:tool-question-answers {building : The id of the building you would like to process.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send all tool question with its answers to Econobis.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $building = Building::findOrFail($this->argument('building'));

        $applicableToolQuestions = [
            'current' => [
                "building-type-category",
                "building-type",
                "building-contract-type",
                "build-year",
                "building-layers",
                "roof-type",
                "monument",
                "energy-label",
                "surface",
                "resident-count",
                "water-comfort",
                "cook-type",
                "amount-gas",
                "amount-electricity",
                "current-wall-insulation",
                "current-floor-insulation",
                "current-roof-insulation",
                "current-living-rooms-windows",
                "current-sleeping-rooms-windows",
                "heat-source",
                "heat-source-other",
                "heat-source-warm-tap-water",
                "heat-source-warm-tap-water-other",
                "boiler-type",
                "boiler-placed-date",
                "heat-pump-type",
                "heat-pump-placed-date",
                "building-heating-application",
                "building-heating-application-other",
                "boiler-setting-comfort-heat",
                "ventilation-type",
                "ventilation-demand-driven",
                "ventilation-heat-recovery",
                "crack-sealing-type",

                "ventilation-type",
                "has-cavity-wall",
                "wall-surface",
                "apply-led-light-how",
                "total-window-surface",
                "frame-type",
                "floor-surface",
                "apply-led-light-how",
                "current-roof-types",
                "is-pitched-roof-insulated",
                "pitched-roof-surface",
                "pitched-roof-insulation",
                "pitched-roof-heating",
                "is-flat-roof-insulated",
                "flat-roof-surface",
                "flat-roof-insulation",
                "flat-roof-heating",

                "has-solar-panels",
                "solar-panel-count",
                "total-installed-power",
                "solar-panels-placed-date",

                "apply-led-light-how",
                "heat-pump-preferred-power",
                "outside-unit-space",
                "inside-unit-space",
                "heater-pv-panel-orientation",
                "heater-pv-panel-angle",
                "hrpp-glass-only-current-glass",
                "hrpp-glass-only-replacement-glass-surface",
                "hrpp-glass-only-replacement-glass-count",
                "hrpp-glass-frame-current-glass",
                "hrpp-glass-frame-replacement-glass-surface",
                "hrpp-glass-frame-replacement-glass-count",
                "hr3p-glass-frame-current-glass",
                "hr3p-glass-frame-rooms-heated",
                "hr3p-glass-frame-replacement-glass-surface",
                "hr3p-glass-frame-replacement-glass-count",
                "glass-in-lead-replace-current-glass",
                "glass-in-lead-replace-rooms-heated",
                "glass-in-lead-replace-glass-surface",
                "glass-in-lead-replace-glass-count"
            ],
            'new' => [
                "new-water-comfort",
                "new-heat-source",
                "new-heat-source-warm-tap-water",
                "new-building-heating-application",
                "new-boiler-type",
                "new-boiler-setting-comfort-heat",
                "new-cook-type",
                "new-heat-pump-type",

                'insulation-wall-surface',
                "flat-roof-insulation-surface",
                "pitched-roof-insulation-surface",

                "solar-panel-peak-power",
                "desired-solar-panel-count",
                "solar-panel-orientation",
                "solar-panel-angle",
            ],
        ];


        $inputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
        foreach ($applicableToolQuestions as $situation => $toolQuestionShorts) {
            foreach ($toolQuestionShorts as $index => $toolQuestionShort) {
                $answers = [];
                $toolQuestion = ToolQuestion::findByShort($toolQuestionShort);

                $values = $toolQuestion->getQuestionValues();

                $buildingAnswers = $building->getAnswer($inputSource, $toolQuestion);

                $applicableToolQuestions[$situation][$toolQuestionShort] = [
                    'id' => $toolQuestion->id,
                    'short' => $toolQuestion->short,
                    'name' => $toolQuestion->name,
                ];

                if ($values->isNotEmpty()) {
                    if (is_array($buildingAnswers)) {
                        foreach ($buildingAnswers as $buildingAnswer) {
                            $buildingAnswerRelatedData = $values->where('value', $buildingAnswer)->first();
                            $answers[] = Arr::only($buildingAnswerRelatedData, ['value', 'name', 'short']);
                        }
                    } else {
                        if ( ! is_null($buildingAnswers)) {
                            $buildingAnswerRelatedData = $values->where('value', $buildingAnswers)->first();
                            $answers = Arr::only($buildingAnswerRelatedData, ['value', 'name', 'short']);
                            if ( ! isset($answers['short'])) {
                                $answers['short'] = null;
                            }
                        }
                    }
                } else {
                    if ( ! is_null($buildingAnswers)) {
                        $answers = [
                            'value' => $buildingAnswers,
                            'short' => null,
                            'name' => null
                        ];
                    }
                }

                $applicableToolQuestions[$situation][$toolQuestionShort]['answers'] = $answers;

                unset($applicableToolQuestions[$situation][$index]);
            }
        }

        $logger = \Illuminate\Support\Facades\Log::getLogger();
        $client = Client::init($logger);
        $econobis = Econobis::init($client);

        $response = $econobis->hoomdossier()->gebruik([
            'account_related' => [
                'building_id' => $building->id,
                'user_id' => $building->user->id,
                'account_id' => $building->user->account_id,
                'contact_id' => $building->user->extra['contact_id'] ?? null,
            ],
            'tool_questions' => $applicableToolQuestions
        ]);

        Log::debug('Response', $response);

        return 0;
    }
}
