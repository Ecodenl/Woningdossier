<?php

use App\Models\SufferFrom;
use Illuminate\Database\Seeder;

class SufferFromsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $suffersFrom = [
            'Ja, weinig',
            'Ja, veel',
            'Nee',
            'Anders uitleg',
        ];

        foreach ($suffersFrom as $sufferFrom) {
            SufferFrom::create([
                'name' => $sufferFrom,
            ]);
        }
    }
}
