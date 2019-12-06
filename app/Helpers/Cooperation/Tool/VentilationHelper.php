<?php

namespace App\Helpers\Cooperation\Tool;

class VentilationHelper {

    /**
     * Method to return the answer options of the how question.
     *
     * @return array
     */
    public static function getHowValues(): array
    {
        $howValues = [
            'windows-doors' => 'Ventilatieroosters in ramen / deuren',
            'other'         => 'Ventilatieroosters overig',
            'windows'       => '(Klep)ramen',
            'none'          => 'Geen ventilatievoorzieningen',
        ];

        return $howValues;
    }

    /**
     * Method to return the answer options of the living situation question.
     *
     * @return array
     */
    public static function getLivingSituationValues(): array
    {
        $livingSituationValues = [
            'dry-laundry'       => 'Ik droog de was in huis',
            'fireplace'         => 'Ik stook een open haard of houtkachel',
            'combustion-device' => 'Ik heb een open verbrandingstoestel',
            'moisture'          => 'Ik heb last van schimmel op de muren',
        ];

        return $livingSituationValues;
    }

    /**
     * Method to return the answer options of the usage question.
     *
     * @return array
     */
    public static function getUsageValues(): array
    {
        $usageValues = [
            'sometimes-off'      => 'Ik zet de ventilatie unit wel eens helemaal uit',
            'no-maintenance'     => 'Ik doe geen onderhoud op de ventilatie unit',
            'filter-replacement' => 'Het filter wordt niet of onregelmatig vervangen',
            'closed'             => 'Ik heb de roosters / klepramen voor aanvoer van buitenlucht vaak dicht staan',
        ];

        return $usageValues;
    }
}