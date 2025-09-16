<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \Spatie\Permission\Models\Permission::updateOrCreate(
            [
                'name' => 'assign role coach and resident',
            ],
            [
                'name' => 'assign role coach and resident',
                'guard_name' => 'web',
            ]
        );
    }
}
