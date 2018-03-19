<?php

use Illuminate\Database\Seeder;
use App\Models\ComfortComplaints;

class ComfortComplaintsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $comfortComplaints = [
            'Tocht',
            'Koude vloer',
            'Koude gevels',
            'Kou vanaf de ramen',
            'Koude dagverdieping',
            'Warmteoverlast',
            'Anders: uitleg',
        ];

        foreach ($comfortComplaints as $comfortComplaint) {
            ComfortComplaints::create([
                'name' => $comfortComplaint,
            ]);
        }
    }
}
