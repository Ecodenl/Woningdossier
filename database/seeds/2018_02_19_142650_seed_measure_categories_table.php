<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SeedMeasureCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $measure = 1;
        While ($measure <16) {
            if ($measure ==1) {
                $measure_categories = [
                    'Isolatie van de vloer',
                    'Isolatie van de bodem',
                    'Isolatie van de kruipruimte',
                    'Leiding isolatie kruipruimte',
                ];
            }elseif ($measure ==2) {
                $measure_categories = [
                    'Gevel isolatie spouw',
                    'Gevel isolatie  binnenzijde',
                    'Gevel isolatie buitenzijde',
                 ];
            }elseif ($measure ==3) {
                $measure_categories = [
                    'Isolatie hellend dak binnen',
                    'Isolatie hellend dak, buiten (vervangen dakpannen, bitume isolerend onderdak etc)',
                    'Isolatie plat dak, buiten (op huidige dakbedekking)',
                    'Isolatie plat dak, buiten (vervanging huidige dakbedekking)',
                    'Vegetatiedak',
                    'Isolatie zoldervloer, bovenop',
                    'Isolatie zoldervloer, tussen plafond',
                ];
            }elseif ($measure ==4) {
                $measure_categories = [
                    'Glas-in-lood',
                    'Plaatsen isolatieglas, alleen beglazing',
                    'Plaatsen isolatieglas, inclusief kozijn',
                    'Plaatsen geïsoleerd kozijn met triple glas',
                    'Plaatsen achterzetbeglazing',
                ];
            }elseif ($measure ==5) {
                $measure_categories = [
                    'Kierdichting ramen en deuren',
                    'Kierdichting aansluiting kozijn en muur',
                    'Kierdichting aansluiting dak en muur',
                    'Kierdichting aansluiting nok',
                    'Kierdichting kruipluik, houten vloer',
                ];
            }elseif ($measure ==6) {
                $measure_categories = [
                    'Ventilatie roosters',
                    'Vraag gestuurde ventilatie roosters',
                    'Ventilatie lucht/water warmtepomp',
                    'Decentrale wtw',
                    'Centrale wtw',
                    'Gelijkstroom ventilatiebox',

                ];
            }elseif ($measure ==7) {
                $measure_categories = [
                    'Ketelvervanging',
                    'Waterzijdig inregelen',
                    'Thermostaatknoppen',
                    'Weersafhankelijke regeling',
                    'Slimme thermostaat (thermosmart, nest, tado,…) opentherm of aan/uit',
                    'Zone indeling',
                    'Isolatie leiding onverwarmde ruimte',
                ];
            }elseif ($measure ==8) {
                $measure_categories = [
                    'Gevel isolatie spouw',
                    'Gevel isolatie  binnenzijde',
                    'Gevel isolatie buitenzijde',
                ];
            }elseif ($measure ==9) {
                $measure_categories = [
                    'Hybride (bron lucht)',
                    'Volledig (bron lucht)',
                    'Volledig (bron bodem)',
                    'Volledig (bron ventilatielucht)',
                    'Warmtepompboiler (tbv tapwater)',
                    'Smart grid compatibel',

                ];
            }elseif ($measure ==10) {
                $measure_categories = [
                    'Pelletketel',
                    'Pelletkachel',
                    'Massakachel (Tulikivi, Ortner)',
                    'Cv-gekoppeld',
                    'Hoogrendementshoutkachel (laag emissie fijn stof)',

                ];
            }elseif ($measure ==11) {
                $measure_categories = [
                    'Laag temperatuur vloerverwarming',
                    'Laag temperatuur wandverwarming',
                    'Laag temperatuur convectoren',
                    'tralingspanelen',
                    'Luchtverwarming',
                    'Radiatoren (laag regime 55-45)',
                ];
            }elseif ($measure ==11) {


            }elseif ($measure ==12) {
                $measure_categories = [
                    'Vacuumbuiscollector',
                    'Vlakkeplaat',
                    'Voorverwarming SWW',
                    'SWW naverwarming',
                    'SWW + verwarmingsondersteuning',

                ];
            }elseif ($measure ==13) {

            }elseif ($measure ==14) {
                $measure_categories = [
                    'Thermische opslag',
                    'Huisbatterij',
                    'Koppeling elektrische auto',
                ];
            }elseif ($measure ==15){
                $measure_categories = [
                    'Spaardouche',
                    'Douche wtw',
                    'Hotfill',
                    'LED verlichting',
                    'Witgoed',
                ];
            }

            foreach ($measure_categories as $measure_categorie) {
                DB::table('measure_categories')->insert([
                        ['measure' => $measure,
                        'name' => $measure_categorie],
                    ]
                );
            }
            $measure++;
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('measure_categories', function (Blueprint $table) {
            //
        });
    }
}
