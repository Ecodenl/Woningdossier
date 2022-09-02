<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class HeatPumpCharacteristicsTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $high  = DB::table('tool_question_custom_values')->where(
            'short',
            '=',
            'temp-high'
        )->first()->id;
        $fifty = DB::table('tool_question_custom_values')->where(
            'short',
            '=',
            'temp-50'
        )->first()->id;
        $low   = DB::table('tool_question_custom_values')->where(
            'short',
            '=',
            'temp-low'
        )->first()->id;

        $characteristics = [
            // 'Hybride warmtepomp met buitenlucht',
            [
                'heat_pump_configurable_type'   => \App\Models\ServiceValue::class,
                'heat_pump_configurable_id'     => $this->getServiceValueIdByNLValue(
                    'Hybride warmtepomp met buitenlucht'
                ),
                'tool_question_custom_value_id' => $high,
                'scop'                          => 3.3,
                'scop_tap_water'                => 2.56,
                'share_percentage_tap_water'    => 0,
                'costs'                         => 4500,
                'standard_power_kw'              => 4,
                'type'                          => \App\Models\HeatPumpCharacteristic::TYPE_HYBRID,
            ],
            [
                'heat_pump_configurable_type'   => \App\Models\ServiceValue::class,
                'heat_pump_configurable_id'     => $this->getServiceValueIdByNLValue(
                    'Hybride warmtepomp met buitenlucht'
                ),
                'tool_question_custom_value_id' => $fifty,
                'scop'                          => 3.3,
                'scop_tap_water'                => 2.56,
                'share_percentage_tap_water'    => 0,
                'costs'                         => 4500,
                'standard_power_kw'              => 4,
                'type'                          => \App\Models\HeatPumpCharacteristic::TYPE_HYBRID,
            ],
            [
                'heat_pump_configurable_type'   => \App\Models\ServiceValue::class,
                'heat_pump_configurable_id'     => $this->getServiceValueIdByNLValue(
                    'Hybride warmtepomp met buitenlucht'
                ),
                'tool_question_custom_value_id' => $low,
                'scop'                          => 4.5,
                'scop_tap_water'                => 2.56,
                'share_percentage_tap_water'    => 0,
                'costs'                         => 4500,
                'standard_power_kw'              => 4,
                'type'                          => \App\Models\HeatPumpCharacteristic::TYPE_HYBRID,
            ],
            // 'Hybride warmtepomp met ventilatielucht'
            [
                'heat_pump_configurable_type'   => \App\Models\ServiceValue::class,
                'heat_pump_configurable_id'     => $this->getServiceValueIdByNLValue(
                    'Hybride warmtepomp met ventilatielucht'
                ),
                'tool_question_custom_value_id' => $high,
                'scop'                          => 4.5,
                'scop_tap_water'                => 2.56,
                'share_percentage_tap_water'    => 0,
                'costs'                         => 3500,
                'standard_power_kw'              => 2,
                'type'                          => \App\Models\HeatPumpCharacteristic::TYPE_HYBRID,
            ],
            [
                'heat_pump_configurable_type'   => \App\Models\ServiceValue::class,
                'heat_pump_configurable_id'     => $this->getServiceValueIdByNLValue(
                    'Hybride warmtepomp met ventilatielucht'
                ),
                'tool_question_custom_value_id' => $fifty,
                'scop'                          => 4.2,
                'scop_tap_water'                => 2.56,
                'share_percentage_tap_water'    => 0,
                'costs'                         => 3500,
                'standard_power_kw'              => 2,
                'type'                          => \App\Models\HeatPumpCharacteristic::TYPE_HYBRID,
            ],
            [
                'heat_pump_configurable_type'   => \App\Models\ServiceValue::class,
                'heat_pump_configurable_id'     => $this->getServiceValueIdByNLValue(
                    'Hybride warmtepomp met ventilatielucht'
                ),
                'tool_question_custom_value_id' => $low,
                'scop'                          => 4.2,
                'scop_tap_water'                => 2.56,
                'share_percentage_tap_water'    => 0,
                'costs'                         => 3500,
                'standard_power_kw'              => 2,
                'type'                          => \App\Models\HeatPumpCharacteristic::TYPE_HYBRID,
            ],
            // 'Hybride warmtepomp met pvt panelen'
            [
                'heat_pump_configurable_type'   => \App\Models\ServiceValue::class,
                'heat_pump_configurable_id'     => $this->getServiceValueIdByNLValue(
                    'Hybride warmtepomp met pvt panelen'
                ),
                'tool_question_custom_value_id' => $high,
                'scop'                          => 2.7,
                'scop_tap_water'                => 2.56,
                'share_percentage_tap_water'    => 0,
                'costs'                         => 11000,
                'standard_power_kw'              => 4,
                'type'                          => \App\Models\HeatPumpCharacteristic::TYPE_HYBRID,
            ],
            [
                'heat_pump_configurable_type'   => \App\Models\ServiceValue::class,
                'heat_pump_configurable_id'     => $this->getServiceValueIdByNLValue(
                    'Hybride warmtepomp met pvt panelen'
                ),
                'tool_question_custom_value_id' => $fifty,
                'scop'                          => 4.2,
                'scop_tap_water'                => 2.56,
                'share_percentage_tap_water'    => 0,
                'costs'                         => 11000,
                'standard_power_kw'              => 4,
                'type'                          => \App\Models\HeatPumpCharacteristic::TYPE_HYBRID,
            ],
            [
                'heat_pump_configurable_type'   => \App\Models\ServiceValue::class,
                'heat_pump_configurable_id'     => $this->getServiceValueIdByNLValue(
                    'Hybride warmtepomp met pvt panelen'
                ),
                'tool_question_custom_value_id' => $low,
                'scop'                          => 5.5,
                'scop_tap_water'                => 2.56,
                'share_percentage_tap_water'    => 0,
                'costs'                         => 11000,
                'standard_power_kw'              => 4,
                'type'                          => \App\Models\HeatPumpCharacteristic::TYPE_HYBRID,
            ],
            // 'Volledige warmtepomp buitenlucht'
            [
                'heat_pump_configurable_type'   => \App\Models\ServiceValue::class,
                'heat_pump_configurable_id'     => $this->getServiceValueIdByNLValue(
                    'Volledige warmtepomp met buitenlucht'
                ),
                'tool_question_custom_value_id' => $high,
                'scop'                          => 2.5,
                'scop_tap_water'                => 2.5,
                'share_percentage_tap_water'    => 100,
                'costs'                         => 12000,
                'standard_power_kw'              => 12,
                'type'                          => \App\Models\HeatPumpCharacteristic::TYPE_FULL,
            ],
            [
                'heat_pump_configurable_type'   => \App\Models\ServiceValue::class,
                'heat_pump_configurable_id'     => $this->getServiceValueIdByNLValue(
                    'Volledige warmtepomp met buitenlucht'
                ),
                'tool_question_custom_value_id' => $fifty,
                'scop'                          => 3.3,
                'scop_tap_water'                => 2.56,
                'share_percentage_tap_water'    => 100,
                'costs'                         => 12000,
                'standard_power_kw'              => 12,
                'type'                          => \App\Models\HeatPumpCharacteristic::TYPE_FULL,
            ],
            [
                'heat_pump_configurable_type'   => \App\Models\ServiceValue::class,
                'heat_pump_configurable_id'     => $this->getServiceValueIdByNLValue(
                    'Volledige warmtepomp met buitenlucht'
                ),
                'tool_question_custom_value_id' => $low,
                'scop'                          => 4.5,
                'scop_tap_water'                => 2.56,
                'share_percentage_tap_water'    => 100,
                'costs'                         => 12000,
                'standard_power_kw'              => 12,
                'type'                          => \App\Models\HeatPumpCharacteristic::TYPE_FULL,
            ],
            // 'Volledige warmtepomp bodemwarmte'
            [
                'heat_pump_configurable_type'   => \App\Models\ServiceValue::class,
                'heat_pump_configurable_id'     => $this->getServiceValueIdByNLValue(
                    'Volledige warmtepomp met bodemwarmte'
                ),
                'tool_question_custom_value_id' => $high,
                'scop'                          => 2.7,
                'scop_tap_water'                => 2.7,
                'share_percentage_tap_water'    => 100,
                'costs'                         => 29000,
                'standard_power_kw'              => 12,
                'type'                          => \App\Models\HeatPumpCharacteristic::TYPE_FULL,
            ],
            [
                'heat_pump_configurable_type'   => \App\Models\ServiceValue::class,
                'heat_pump_configurable_id'     => $this->getServiceValueIdByNLValue(
                    'Volledige warmtepomp met bodemwarmte'
                ),
                'tool_question_custom_value_id' => $fifty,
                'scop'                          => 4.5,
                'scop_tap_water'                => 2.7,
                'share_percentage_tap_water'    => 100,
                'costs'                         => 29000,
                'standard_power_kw'              => 12,
                'type'                          => \App\Models\HeatPumpCharacteristic::TYPE_FULL,
            ],
            [
                'heat_pump_configurable_type'   => \App\Models\ServiceValue::class,
                'heat_pump_configurable_id'     => $this->getServiceValueIdByNLValue(
                    'Volledige warmtepomp met bodemwarmte'
                ),
                'tool_question_custom_value_id' => $low,
                'scop'                          => 5.5,
                'scop_tap_water'                => 2.7,
                'share_percentage_tap_water'    => 100,
                'costs'                         => 29000,
                'standard_power_kw'              => 12,
                'type'                          => \App\Models\HeatPumpCharacteristic::TYPE_FULL,
            ],
            // 'Volledige warmtepomp met pvt panelen'
            [
                'heat_pump_configurable_type'   => \App\Models\ServiceValue::class,
                'heat_pump_configurable_id'     => $this->getServiceValueIdByNLValue(
                    'Volledige warmtepomp met pvt panelen'
                ),
                'tool_question_custom_value_id' => $high,
                'scop'                          => 2.7,
                'scop_tap_water'                => 2.7,
                'share_percentage_tap_water'    => 100,
                'costs'                         => 24000,
                'standard_power_kw'              => 12,
                'type'                          => \App\Models\HeatPumpCharacteristic::TYPE_FULL,
            ],
            [
                'heat_pump_configurable_type'   => \App\Models\ServiceValue::class,
                'heat_pump_configurable_id'     => $this->getServiceValueIdByNLValue(
                    'Volledige warmtepomp met pvt panelen'
                ),
                'tool_question_custom_value_id' => $fifty,
                'scop'                          => 4.5,
                'scop_tap_water'                => 2.7,
                'share_percentage_tap_water'    => 100,
                'costs'                         => 24000,
                'standard_power_kw'              => 12,
                'type'                          => \App\Models\HeatPumpCharacteristic::TYPE_FULL,
            ],
            [
                'heat_pump_configurable_type'   => \App\Models\ServiceValue::class,
                'heat_pump_configurable_id'     => $this->getServiceValueIdByNLValue(
                    'Volledige warmtepomp met pvt panelen'
                ),
                'tool_question_custom_value_id' => $low,
                'scop'                          => 5.5,
                'scop_tap_water'                => 2.7,
                'share_percentage_tap_water'    => 100,
                'costs'                         => 24000,
                'standard_power_kw'              => 12,
                'type'                          => \App\Models\HeatPumpCharacteristic::TYPE_FULL,
            ],
            // warmtepompboiler
            [
                'heat_pump_configurable_type'   => \App\Models\ToolQuestionCustomValue::class,
                'heat_pump_configurable_id'     => $this->getToolQuestionCustomValueByShort(
                    'heat-pump-boiler'
                ),
                'tool_question_custom_value_id' => null,
                'scop'                          => 0.0,
                'scop_tap_water'                => 2.56,
                'share_percentage_tap_water'    => 100,
                'costs'                         => 25000,
                'standard_power_kw'              => 2,
                'type'                          => \App\Models\HeatPumpCharacteristic::TYPE_HYBRID,
            ],
        ];

        foreach ($characteristics as $characteristic) {
            DB::table('heat_pump_characteristics')->updateOrInsert(
                Arr::only(
                    $characteristic,
                    [
                        'heat_pump_configurable_type',
                        'heat_pump_configurable_id',
                        'tool_question_custom_value_id',
                    ]
                ),
                $characteristic
            );
        }
    }

    protected function getServiceValueIdByNLValue(string $value, $locale = 'nl')
    {
        $serviceValue = DB::table('service_values')->where(
            "value->{$locale}",
            $value
        )->first();

        return $serviceValue->id ?? null;
    }

    protected function getToolQuestionCustomValueByShort(string $value)
    {
        $toolQuestionCustomValue = DB::table(
            'tool_question_custom_values'
        )->where(
            "short",
            $value
        )->first();

        return $toolQuestionCustomValue->id ?? null;
    }


}
