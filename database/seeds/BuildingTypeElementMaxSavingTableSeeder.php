<?php

use Illuminate\Database\Seeder;

class BuildingTypeElementMaxSavingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $maxSavings = [
            'Vrijstaande woning' => [
                'Ramen in de leefruimtes' => 15,
                'Ramen in de slaapruimtes' => 15,
                'Gevelisolatie' => 35,
                'Vloerisolatie' => 15,
                'Dakisolatie' => 25,
            ],
            '2 onder 1 kap' => [
                'Ramen in de leefruimtes' => 20,
                'Ramen in de slaapruimtes' => 20,
                'Gevelisolatie' => 30,
                'Vloerisolatie' => 15,
                'Dakisolatie' => 25,
            ],
            'Hoekwoning' => [
                'Ramen in de leefruimtes' => 25,
                'Ramen in de slaapruimtes' => 25,
                'Gevelisolatie' => 30,
                'Vloerisolatie' => 15,
                'Dakisolatie' => 20,
            ],
            'Tussenwoning' => [
                'Ramen in de leefruimtes' => 25,
                'Ramen in de slaapruimtes' => 25,
                'Gevelisolatie' => 20,
                'Vloerisolatie' => 15,
                'Dakisolatie' => 30,
            ],
            'Benedenwoning hoek' => [
                'Ramen in de leefruimtes' => 25,
                'Ramen in de slaapruimtes' => 25,
                'Gevelisolatie' => 30,
                'Vloerisolatie' => 15,
                'Dakisolatie' => 20,
            ],
            'Benedenwoning tussen' => [
                'Ramen in de leefruimtes' => 25,
                'Ramen in de slaapruimtes' => 25,
                'Gevelisolatie' => 20,
                'Vloerisolatie' => 15,
                'Dakisolatie' => 30,
            ],
            'Bovenwoning hoek' => [
                'Ramen in de leefruimtes' => 25,
                'Ramen in de slaapruimtes' => 25,
                'Gevelisolatie' => 30,
                'Vloerisolatie' => 15,
                'Dakisolatie' => 20,
            ],
            'Bovenwoning tussen' => [
                'Ramen in de leefruimtes' => 25,
                'Ramen in de slaapruimtes' => 25,
                'Gevelisolatie' => 20,
                'Vloerisolatie' => 15,
                'Dakisolatie' => 20,
            ],
            'Appartement tussen op een tussenverdieping' => [
                'Ramen in de leefruimtes' => 25,
                'Ramen in de slaapruimtes' => 25,
                'Gevelisolatie' => 20,
                'Vloerisolatie' => 15,
                'Dakisolatie' => 30,
            ],
            'Appartement hoek op een tussenverdieping' => [
                'Ramen in de leefruimtes' => 25,
                'Ramen in de slaapruimtes' => 25,
                'Gevelisolatie' => 30,
                'Vloerisolatie' => 15,
                'Dakisolatie' => 20,
            ],
        ];

        foreach ($maxSavings as $buildingType => $elements) {
            $bt = \DB::table('building_types')->leftJoin('translations', 'building_types.name', '=', 'translations.key')
                ->where('language', 'nl')
                ->where('translation', $buildingType)
                ->first(['building_types.id']);

            $buildingTypeId = $bt->id;

            foreach ($elements as $element => $percentage) {
                $el = \DB::table('elements')->leftJoin('translations', 'elements.name', '=', 'translations.key')
                    ->where('language', 'nl')
                    ->where('translation', $element)
                    ->first(['elements.id']);

                $elementId = $el->id;

                \DB::table('building_type_element_max_savings')->insert([
                    'building_type_id' => $buildingTypeId,
                    'element_id' => $elementId,
                    'max_saving' => $percentage,
                ]);
            }
        }
    }
}
