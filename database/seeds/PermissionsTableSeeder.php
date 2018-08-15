<?php

use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Spatie\Permission\Models\Permission::updateOrCreate(
            [
                'name' => 'assign role coach and resident'
            ],
            [
                'name' => 'assign role coach and resident',
                'guard_name' => 'web'
            ]
        );
    }
}
