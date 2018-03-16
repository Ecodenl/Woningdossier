<?php

use Illuminate\Database\Seeder;
use App\Models\PresentWindow;

class PresentWindowsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $presentWindows = [
            [
                'name' => 'Enkelglas',
                'calculate_value' => 1,
            ],
            [
                'name' => 'Dubbelglas',
                'calculate_value' => 2,
            ],
            [
                'name' => 'HR++ glas',
                'calculate_value' => 3,
            ],
            [
                'name' => 'Drievoudige beglazing',
                'calculate_value' => 4,
            ],
        ];

        foreach ($presentWindows as $presentWindow) {
            PresentWindow::create([
                'name' => $presentWindow['name'],
                'calculate_value' => $presentWindow['calculate_value'],
            ]);
        }
    }
}
