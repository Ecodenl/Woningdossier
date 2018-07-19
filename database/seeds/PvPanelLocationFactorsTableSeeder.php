<?php

use Illuminate\Database\Seeder;

class PvPanelLocationFactorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    //$table->integer('pc2', 2)->unsigned();
	    //$table->string('location');
	    //$table->decimal('factor', 3, 2);

	    $locations = [
			[
				'pc2' => 17,
				'location' => 'Den Helder',
				'factor' => 1.00,
			],
		    [
			    'pc2' => 18,
			    'location' => 'Alkmaar',
			    'factor' => 1.00,
		    ],
		    [
			    'pc2' => 19,
			    'location' => 'Heemskerk',
			    'factor' => 1.00,
		    ],
		    [
			    'pc2' => 20,
			    'location' => 'Haarlem',
			    'factor' => 1.00,
		    ],
		    [
			    'pc2' => 22,
			    'location' => 'Katwijk',
			    'factor' => 1.00,
		    ],
		    [
			    'pc2' => 25,
			    'location' => 'Den Haag',
			    'factor' => 1.00,
		    ],
		    [
			    'pc2' => 43,
			    'location' => 'Middelburg',
			    'factor' => 1.00,
		    ],
		    [
			    'pc2' => 44,
			    'location' => 'Goes',
			    'factor' => 1.00,
		    ],
		    [
			    'pc2' => 45,
			    'location' => 'Terneuzen',
			    'factor' => 1.00,
		    ],
		    [
			    'pc2' => 87,
			    'location' => 'Bolsward',
			    'factor' => 1.00,
		    ],
		    [
			    'pc2' => 88,
			    'location' => 'Franeker',
			    'factor' => 1.00,
		    ],
		    [
			    'pc2' => 10,
			    'location' => 'Amsterdam',
			    'factor' => 0.95,
		    ],
		    [
			    'pc2' => 11,
			    'location' => 'Amstelveen',
			    'factor' => 0.95,
		    ],
		    [
			    'pc2' => 14,
			    'location' => 'Bussum/Purmerend',
			    'factor' => 0.95,
		    ],
		    [
			    'pc2' => 15,
			    'location' => 'Zaanstad',
			    'factor' => 0.95,
		    ],
		    [
			    'pc2' => 16,
			    'location' => 'Hoorn',
			    'factor' => 0.95,
		    ],
		    [
			    'pc2' => 21,
			    'location' => 'Nieuw-Vennep',
			    'factor' => 0.95,
		    ],
		    [
			    'pc2' => 23,
			    'location' => 'Leiden',
			    'factor' => 0.95,
		    ],
		    [
			    'pc2' => 24,
			    'location' => 'Alphen a/d Rijn',
			    'factor' => 0.95,
		    ],
		    [
			    'pc2' => 26,
			    'location' => 'Delft',
			    'factor' => 0.95,
		    ],
		    [
			    'pc2' => 27,
			    'location' => 'Zoetermeer',
			    'factor' => 0.95,
		    ],
		    [
			    'pc2' => 30,
			    'location' => 'Rotterdam',
			    'factor' => 0.95,
		    ],
		    [
			    'pc2' => 31,
			    'location' => 'Vlaardingen',
			    'factor' => 0.95,
		    ],
		    [
			    'pc2' => 32,
			    'location' => 'Spijkenisse/Goeree',
			    'factor' => 0.95,
		    ],
		    [
			    'pc2' => 46,
			    'location' => 'Bergen op Zoom',
			    'factor' => 0.95,
		    ],
		    [
			    'pc2' => 86,
			    'location' => 'Sneek',
			    'factor' => 0.95,
		    ],
		    [
			    'pc2' => 89,
			    'location' => 'Leeuwarden',
			    'factor' => 0.95,
		    ],
		    [
			    'pc2' => 90,
			    'location' => 'Grouw',
			    'factor' => 0.95,
		    ],
		    [
			    'pc2' => 91,
			    'location' => 'Dokkum',
			    'factor' => 0.95,
		    ],
		    [
			    'pc2' => 29,
			    'location' => 'Ridderkerk',
			    'factor' => 0.93,
		    ],
		    [
			    'pc2' => 33,
			    'location' => 'Dordrecht',
			    'factor' => 0.93,
		    ],
		    [
			    'pc2' => 34,
			    'location' => 'Woerden',
			    'factor' => 0.93,
		    ],
		    [
			    'pc2' => 36,
			    'location' => 'Mijdrecht',
			    'factor' => 0.93,
		    ],
		    [
			    'pc2' => 47,
			    'location' => 'Roosendaal',
			    'factor' => 0.93,
		    ],
		    [
			    'pc2' => 48,
			    'location' => 'Breda',
			    'factor' => 0.93,
		    ],
		    [
			    'pc2' => 49,
			    'location' => 'Oosterhout',
			    'factor' => 0.93,
		    ],
		    [
			    'pc2' => 61,
			    'location' => 'Sittard',
			    'factor' => 0.93,
		    ],
		    [
			    'pc2' => 62,
			    'location' => 'Maastricht',
			    'factor' => 0.93,
		    ],
		    [
			    'pc2' => 63,
			    'location' => 'Valkenburg',
			    'factor' => 0.93,
		    ],
		    [
			    'pc2' => 64,
			    'location' => 'Heerlen',
			    'factor' => 0.93,
		    ],
		    [
			    'pc2' => 70,
			    'location' => 'Doetinchem',
			    'factor' => 0.93,
		    ],
		    [
			    'pc2' => 71,
			    'location' => 'Winterswijk',
			    'factor' => 0.93,
		    ],
		    [
			    'pc2' => 82,
			    'location' => 'Lelystad',
			    'factor' => 0.93,
		    ],
		    [
			    'pc2' => 85,
			    'location' => 'Joure',
			    'factor' => 0.93,
		    ],
		    [
			    'pc2' => 35,
			    'location' => 'Utrecht',
			    'factor' => 0.88,
		    ],
		    [
			    'pc2' => 37,
			    'location' => 'Soest/Zeist/Barneveld',
			    'factor' => 0.91,
		    ],
		    [
			    'pc2' => 38,
			    'location' => 'Amersfoort',
			    'factor' => 0.91,
		    ],
		    [
			    'pc2' => 39,
			    'location' => 'Doorn',
			    'factor' => 0.91,
		    ],
		    [
			    'pc2' => 40,
			    'location' => 'Tiel',
			    'factor' => 0.91,
		    ],
		    [
			    'pc2' => 41,
			    'location' => 'Culemborg',
			    'factor' => 0.91,
		    ],
		    [
			    'pc2' => 42,
			    'location' => 'Gorinchem',
			    'factor' => 0.91,
		    ],
		    [
			    'pc2' => 50,
			    'location' => 'Tilburg',
			    'factor' => 0.91,
		    ],
		    [
			    'pc2' => 51,
			    'location' => 'Waalwijk',
			    'factor' => 0.91,
		    ],
		    [
			    'pc2' => 52,
			    'location' => 'Den Bosch',
			    'factor' => 0.91,
		    ],
		    [
			    'pc2' => 53,
			    'location' => 'Oss',
			    'factor' => 0.91,
		    ],
		    [
			    'pc2' => 73,
			    'location' => 'Apeldoorn',
			    'factor' => 0.91,
		    ],
		    [
			    'pc2' => 80,
			    'location' => 'Zwolle',
			    'factor' => 0.91,
		    ],
		    [
			    'pc2' => 83,
			    'location' => 'Emmeloord',
			    'factor' => 0.91,
		    ],
		    [
			    'pc2' => 84,
			    'location' => 'Heerenveen',
			    'factor' => 0.91,
		    ],
		    [
			    'pc2' => 92,
			    'location' => 'Drachten',
			    'factor' => 0.91,
		    ],
		    [
			    'pc2' => 93,
			    'location' => 'Roden',
			    'factor' => 0.91,
		    ],
		    [
			    'pc2' => 98,
			    'location' => 'Noordhorn',
			    'factor' => 0.91,
		    ],
		    [
			    'pc2' => 54,
			    'location' => 'Uden',
			    'factor' => 0.88,
		    ],
		    [
			    'pc2' => 55,
			    'location' => 'Valkenswaard',
			    'factor' => 0.88,
		    ],
		    [
			    'pc2' => 56,
			    'location' => 'Eindhoven',
			    'factor' => 0.88,
		    ],
		    [
			    'pc2' => 57,
			    'location' => 'Helmond',
			    'factor' => 0.88,
		    ],
		    [
			    'pc2' => 58,
			    'location' => 'Venray',
			    'factor' => 0.88,
		    ],
		    [
			    'pc2' => 59,
			    'location' => 'Venlo',
			    'factor' => 0.88,
		    ],
		    [
			    'pc2' => 60,
			    'location' => 'Roermond',
			    'factor' => 0.88,
		    ],
		    [
			    'pc2' => 65,
			    'location' => 'Nijmegen',
			    'factor' => 0.88,
		    ],
		    [
			    'pc2' => 66,
			    'location' => 'Wijchen/Elst',
			    'factor' => 0.88,
		    ],
		    [
			    'pc2' => 67,
			    'location' => 'Ede/Wageningen',
			    'factor' => 0.88,
		    ],
		    [
			    'pc2' => 68,
			    'location' => 'Arnhem',
			    'factor' => 0.88,
		    ],
		    [
			    'pc2' => 69,
			    'location' => 'Doesburg',
			    'factor' => 0.88,
		    ],
		    [
			    'pc2' => 72,
			    'location' => 'Lochem',
			    'factor' => 0.88,
		    ],
		    [
			    'pc2' => 74,
			    'location' => 'Deventer/Goor',
			    'factor' => 0.88,
		    ],
		    [
			    'pc2' => 75,
			    'location' => 'Enschede',
			    'factor' => 0.88,
		    ],
		    [
			    'pc2' => 76,
			    'location' => 'Almelo',
			    'factor' => 0.88,
		    ],
		    [
			    'pc2' => 77,
			    'location' => 'Hardenberg',
			    'factor' => 0.88,
		    ],
		    [
			    'pc2' => 78,
			    'location' => 'Emmen',
			    'factor' => 0.88,
		    ],
		    [
			    'pc2' => 79,
			    'location' => 'Meppel',
			    'factor' => 0.88,
		    ],
		    [
			    'pc2' => 81,
			    'location' => 'Raalte',
			    'factor' => 0.88,
		    ],
		    [
			    'pc2' => 94,
			    'location' => 'Assen',
			    'factor' => 0.88,
		    ],
		    [
			    'pc2' => 95,
			    'location' => 'Stadskanaal',
			    'factor' => 0.88,
		    ],
		    [
			    'pc2' => 96,
			    'location' => 'Veendam',
			    'factor' => 0.88,
		    ],
		    [
			    'pc2' => 97,
			    'location' => 'Groningen',
			    'factor' => 0.88,
		    ],
		    [
			    'pc2' => 99,
			    'location' => 'Delfzijl',
			    'factor' => 0.88,
		    ],
	    ];

	    foreach($locations as $location){
	    	\DB::table('pv_panel_location_factors')->insert($location);
	    }
    }
}
