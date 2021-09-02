<?php

namespace App\Console\Commands\Upgrade;

use App\Models\BuildingHeatingApplication;
use App\Models\ExampleBuildingContent;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class ConvertExampleBuildingContents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upgrade:convert-example-building-contents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates the example building contents to the new structure';

    const NOT_PRESENT = 'not-present';

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
     * @return mixed
     */
    public function handle()
    {
        $exampleBuildingContents = ExampleBuildingContent::cursor();

        /** @var ExampleBuildingContent $exampleBuildingContent */
        foreach ($exampleBuildingContents as $exampleBuildingContent) {
            $contents = $exampleBuildingContent->content;

            $contents = $this->updateCookGas($contents);
            $contents = $this->updateBuildingHeatingApplication($contents);
            $contents = $this->updateBoiler($contents);
            $contents = $this->updateHeatPump($contents);

            $exampleBuildingContent->content = $contents;
            $exampleBuildingContent->save();
        }
    }

    protected function updateCookGas(array $contents): array
    {
        $this->line("Checking cook_gas");
        $cookGasKey = 'general-data.usage.user_energy_habits.cook_gas';
        $newKey     = 'general-data.usage.tool_question_answers.cook-type';

        $cookGas = data_get(
            $contents,
            $cookGasKey,
            self::NOT_PRESENT
        );
        if ($cookGas !== self::NOT_PRESENT) {
            $cookGas = (int)$cookGas;
            $this->warn("cook_gas found with value: ".$cookGas);

            // now map the actual answer.
            if ($cookGas == 1 || $cookGas == 0) {
                $answer = 'gas';
            } else {
                $answer = 'electric';
            }

            $this->info(
                sprintf(
                    "%s: %s --> %s: %s",
                    $cookGasKey,
                    $cookGas,
                    $newKey,
                    $answer
                )
            );
            Arr::pull($contents, $cookGasKey);
            Arr::set($contents, $newKey, $answer);
        }

        return $contents;
    }

    protected function updateBuildingHeatingApplication(array $contents): array
    {
        $this->line("Checking building heating application");
        $bhKey  = 'general-data.current-state.building_features.building_heating_application_id';
        $newKey = 'general-data.current-state.tool_question_answers.building-heating-application';

        $bhid = data_get(
            $contents,
            $bhKey,
            self::NOT_PRESENT
        );
        if ($bhid !== self::NOT_PRESENT) {
            if (is_null($bhid)) {
                Arr::pull($contents, $bhKey);
            } else {
                $heatingApplication = BuildingHeatingApplication::find($bhid);
                $value = $heatingApplication->short;
//                $valueId            = ToolQuestionCustomValue::findByShort(
//                    $heatingApplication->short
//                )->id;

                $this->info(
                    sprintf(
                        "%s: %s --> %s: %s",
                        $bhKey,
                        $bhid,
                        $newKey,
                        $value
                    )
                );
                Arr::pull($contents, $bhKey);
                Arr::set($contents, $newKey, $value);
            }
        }

        return $contents;
    }

    protected function updateBoiler(array $contents): array
    {
        $this->line("Checking boiler");
        $boilerKey = 'general-data.current-state.service.4';
        $newKey    = 'general-data.current-statee.tool_question_answers.heat-source';

        $boiler = data_get($contents, $boilerKey, self::NOT_PRESENT);

        if ($boiler !== self::NOT_PRESENT) {
            $boiler = (int)$boiler;

            if ($boiler >= 10 && $boiler <= 12) {
                // aanwezig, ..
//                $valueId = ToolQuestionCustomValue::findByShort(
//                    'hr-boiler'
//                )->id;
                $value = 'hr-boiler';

                $this->info(
                    sprintf(
                        "%s: %s --> %s: %s",
                        $boilerKey,
                        $boiler,
                        $newKey,
                        $value
                    )
                );
                Arr::pull($contents, $boilerKey);
                Arr::set($contents, $newKey, $value);
            } else {
                Arr::pull($contents, $boilerKey);
            }
        }

        return $contents;
    }

    protected function updateHeatPump(array $contents): array
    {
        $this->line("Checking heat pump");
        $heatPumpKey = 'general-data.current-state.service.8';
        $newKey      = 'general-data.current-state.tool_question_answers.heat-source';

        $heatPump = data_get($contents, $heatPumpKey, self::NOT_PRESENT);

        if ($heatPump !== self::NOT_PRESENT) {
            $heatPump = (int)$heatPump;

            $heatPumpMap = [
                26 => ['heat-pump'],
                27 => ['heat-pump'],
                28 => ['heat-pump', 'hr-boiler'], // hybrid
                29 => ['heat-pump'],
            ];

            Arr::pull($contents, $heatPumpKey);
            if (array_key_exists($heatPump, $heatPumpMap)) {
                // aanwezig, ..
                // if hr-boiler was already set, remove it now
                $heatSource = Arr::pull($contents, $newKey);

                foreach ($heatPumpMap[$heatPump] as $short) {
                    //$valueId = ToolQuestionCustomValue::findByShort($short)->id;

                    //$heatSource[] = $valueId;
                    $heatSource[]= $short;
                }
                $heatSource = array_unique($heatSource);

                $this->info(
                    sprintf(
                        "%s: %s --> %s: %s",
                        $heatPumpKey,
                        $heatPump,
                        $newKey,
                        json_encode($heatSource)
                    )
                );

                Arr::set($contents, $newKey, $heatSource);
            }
        }

        return $contents;
    }

}
