<?php

use Illuminate\Database\Seeder;

class WoodElementsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $woodElements = [
            'woningdossier.cooperation.tool.insulated-glazing.paint-work.other-wood-elements.element0',
            'woningdossier.cooperation.tool.insulated-glazing.paint-work.other-wood-elements.element1',
            'woningdossier.cooperation.tool.insulated-glazing.paint-work.other-wood-elements.element2',
        ];

        foreach ($woodElements as $woodElement) {
            \App\Models\WoodElement::create(['translation_key' => $woodElement]);
        }
    }
}
