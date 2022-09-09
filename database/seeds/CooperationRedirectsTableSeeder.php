<?php

use Illuminate\Database\Seeder;

class CooperationRedirectsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $redirects = [
            [
                'from_slug' => 'vrijstadenergie',
                // note this slug will be replaced by id
                'cooperation_id' => 'energieloketrivierenland',
            ],
            [
                'from_slug' => 'hnwr',
                // note this slug will be replaced by id
                'cooperation_id' => 'energieloketrivierenland',
            ],
            [
                'from_slug' => 'duurzaamheidsloketwestbrabant',
                // note this slug will be replaced by id
                'cooperation_id' => 'west-brabantwoontslim',
            ],
        ];

        foreach($redirects as $redirect){
            $cooperation = $this->getCooperationIdBySlug($redirect['cooperation_id']);
            $redirect['cooperation_id'] = $cooperation->id;
            DB::table('cooperation_redirects')->updateOrInsert($redirect);
        }
    }

    protected function getCooperationIdBySlug($slug){
        return DB::table('cooperations')->whereRaw('LOWER(slug) = ?', [strtolower($slug)])->first();
    }
}
