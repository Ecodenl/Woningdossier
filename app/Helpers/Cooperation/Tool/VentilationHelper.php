<?php

namespace App\Helpers\Cooperation\Tool;

use App\Calculations\Ventilation;
use App\Models\BuildingVentilation;
use App\Models\MeasureApplication;
use App\Models\Step;
use App\Models\UserActionPlanAdvice;
use App\Services\UserActionPlanAdviceService;

class VentilationHelper extends ToolHelper
{
    public function saveValues(): ToolHelper
    {
        // Save ventilation data
        BuildingVentilation::updateOrCreate(
            [
                'building_id' => $this->building->id,
                'input_source_id' => $this->inputSource->id,
            ],
            [
                'how' => $this->getValues('building_ventilations.how'),
                'usage' => $this->getValues('building_ventilations.usage') ?? [],
                'living_situation' => $this->getValues('building_ventilations.living_situation'),
            ]
        );

        return $this;
    }

    public function createAdvices(): ToolHelper
    {
        $updatedMeasureIds = $this->getValues('updated_measure_ids');

        $step = Step::findByShort('ventilation');

        $energyHabit = $this->user->energyHabit()->forInputSource($this->inputSource)->first();

        $results = Ventilation::calculate($this->building, $this->inputSource, $energyHabit, $this->getValues());

        $oldAdvices = UserActionPlanAdviceService::clearForStep($this->user, $this->inputSource, $step);

        $considerables = $this->getValues('considerables');

        foreach ($considerables as $considerableId => $considerableData) {
            $measureApplication = MeasureApplication::find($considerableId);
            if ($this->considers($measureApplication) && $measureApplication instanceof MeasureApplication) {
                if ('crack-sealing' == $measureApplication->short) {
                    $actionPlanAdvice = new UserActionPlanAdvice($results['result']['crack_sealing'] ?? []);
                    $actionPlanAdvice->costs = ['from' => $results['result']['crack_sealing']['cost_indication'] ?? null]; // only outlier
                } else {
                    $actionPlanAdvice = new UserActionPlanAdvice();
                }

                $actionPlanAdvice->input_source_id = $this->inputSource->id;
                $actionPlanAdvice->planned = true;
                $actionPlanAdvice->user()->associate($this->user);
                $actionPlanAdvice->userActionPlanAdvisable()->associate($measureApplication);
                $actionPlanAdvice->step()->associate($step);

                // We only want to check old advices if the updated attributes are not relevant to this measure
                if (! in_array($measureApplication->id, $updatedMeasureIds)) {
                    UserActionPlanAdviceService::checkOldAdvices($actionPlanAdvice, $measureApplication, $oldAdvices);
                }

                $actionPlanAdvice->save();
            }
        }

        return $this;
    }

    public function createValues(): ToolHelper
    {
        /** @var BuildingVentilation $buildingVentilation */
        $buildingVentilation = $this
            ->building
            ->buildingVentilations()
            ->forInputSource($this->masterInputSource)
            ->first();

        $step = Step::findByShort('ventilation');

        $measureApplications = MeasureApplication::where('step_id', '=', $step->id)
            ->whereIn('short', [
                'ventilation-balanced-wtw',
                'ventilation-decentral-wtw',
                'ventilation-demand-driven',
                'crack-sealing',
            ])
            ->get();

        $considerables = [];

        foreach ($measureApplications as $measureApplication) {
            $considerables[$measureApplication->id] = [
                'is_considering'=> $this->user->considers($measureApplication, $this->masterInputSource),
                'name' => $measureApplication->measure_name
            ];
        }

        $this->setValues([
            'considerables' => $considerables,
            'building_ventilations' => [
                'how' => optional($buildingVentilation)->how,
                'living_situation' => optional($buildingVentilation)->living_situation,
                'usage' => optional($buildingVentilation)->usage,
            ],
            'updated_measure_ids' => [],
        ]);

        return $this;
    }

    /**
     * Method to return the answer options of the how question.
     */
    public static function getHowValues(): array
    {
        $howValues = [
            'windows-doors' => 'Ventilatieroosters in ramen / deuren',
            'other' => 'Ventilatieroosters overig',
            'windows' => '(Klep)ramen',
            'none' => 'Geen ventilatievoorzieningen',
        ];

        return $howValues;
    }

    /**
     * Method to return the answer options of the living situation question.
     */
    public static function getLivingSituationValues(): array
    {
        return [
            'dry-laundry' => 'Ik droog de was in huis',
            'fireplace' => 'Ik stook een open haard of houtkachel',
            'combustion-device' => 'Ik heb een open verbrandingstoestel',
            'moisture' => 'Ik heb last van schimmel op de muren',
        ];
    }

    /**
     * Method to return the answer options of the usage question.
     */
    public static function getUsageValues(): array
    {
        return [
            'sometimes-off' => 'Ik zet de ventilatie unit wel eens helemaal uit',
            'no-maintenance' => 'Ik doe geen onderhoud op de ventilatie unit',
            'filter-replacement' => 'Het filter wordt niet of onregelmatig vervangen',
            'closed' => 'Ik heb de roosters / klepramen voor aanvoer van buitenlucht vaak dicht staan',
        ];
    }

    /**
     * Method to return the warnings for the selected usages.
     *
     * @return array
     */
    public static function getUsageWarnings()
    {
        return [
            'sometimes-off' => 'Laat de ventilatie unit altijd aan staan, anders wordt er helemaal niet geventileerd en hoopt vocht en vieze lucht zich op. Trek alleen bij onderhoud of in geval van een ramp (als de overheid adviseert ramen en deuren te sluiten) de stekker van de ventilatie-unit uit het stopcontact.',
            'no-maintenance' => 'Laat iedere 2 jaar een onderhoudsmonteur langskomen, regelmatig onderhoud van de ventilatie-unit is belangrijk. Kijk in de gebruiksaanwijzing hoe vaak onderhoud aan de unit nodig is.',
            'filter-replacement' => 'Voor een goede luchtkwaliteit is het belangrijk om regelmatig de filter te vervangen. Kijk in de gebruiksaanwijzing hoe vaak de filters vervangen moeten worden.',
            'closed' => 'Zorg dat de roosters in de woonkamer en slaapkamers altijd open staan. Schone lucht in huis is noodzakelijk voor je gezondheid.',
        ];
    }

    /**
     * Method to return the warnings for the selected hows.
     *
     * @return array
     */
    public static function getHowWarnings()
    {
        return [
            'none' => 'Er is op dit moment mogelijkerwijs onvoldoende ventilatie, het kan zinvol zijn om dit door een specialist te laten beoordelen.',
        ];
    }

    /**
     * Method to return the warnings for the selected living situations.
     */
    public static function getLivingSituationWarnings(): array
    {
        return [
            'dry-laundry' => 'Ventileer extra als de was te drogen hangt, door de schakelaar op de hoogste stand te zetten of een raam open te doen. Hang de was zoveel mogelijk buiten te drogen.',
            'fireplace' => 'Zorg voor extra ventilatie tijdens het stoken van open haard of houtkachel, zowel voor de aanvoer van zuurstof als de afvoer van schadelijke stoffen. Zet bijvoorbeeld een (klep)raam open.',
            'combustion-device' => 'Zorg bij een open verbrandingstoestel in ieder geval dat er altijd voldoende luchttoevoer is. Anders kan onvolledige verbranding optreden waarbij het gevaarlijke koolmonoxide kan ontstaan.',
            'moisture' => 'Wanneer u last heeft van schimmel of vocht in huis dan wordt geadviseerd om dit door een specialist te laten beoordelen.',
        ];
    }

    /**
     * Method to return a warning for the selected value / option.
     *
     * @param $value
     *
     * @return mixed
     */
    public static function getWarningForValues($value = null)
    {
        $allWarnings = [];

        $allWarnings = array_merge($allWarnings, self::getHowWarnings(), self::getUsageWarnings(), self::getLivingSituationWarnings());

        if (!is_null($value)) {
            return $allWarnings[$value];
        }

        return $allWarnings;
    }
}
