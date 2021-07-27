<?php

use Illuminate\Database\Seeder;

class SubStepTemplatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $datas = [
            [
                'name'  => ['nl' => 'Vragen over volledige breedte van pagina'],
                'short' => 'template-default',
                'view' => 'template-default'
            ],
            [
                'name'  => ['nl' => 'Welke zaken zou je willen veranderen aan uw woning'],
                'short' => 'template-custom-changes',
                'view' => 'template-custom-changes'
            ],
            [
                'name'  => ['nl' => '1 hoofdvraag 2 bijvragen'],
                'short' => 'template-2-rows-1-top-2-bottom',
                'view' => 'template-2-rows-1-top-2-bottom',
            ],
            [
                'name'  => ['nl' => 'Meerdere hoofdvragen 1 bijvraag'],
                'short' => 'template-2-rows-3-top-1-bottom',
                'view' =>  'template-2-rows-3-top-1-bottom',
            ],
        ];

        foreach ($datas as $data) {
            $data['name'] = json_encode($data['name']);
            DB::table('sub_step_templates')
                ->updateOrInsert(['short' => $data['short']], $data);
        }
    }
}
