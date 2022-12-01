<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SqlDumpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $db = config('database.connections.'.config('database.default'));

        $sqlFiles = [
            // TODO: Check if these are relevant to DB seeding, currently using non-existing translation
            // for example buildings, which causes error when seeding this
//            'database/sql-dumps/example_buildings.sql',
//            'database/sql-dumps/example_building_contents.sql',
        ];
        foreach ($sqlFiles as $sqlFile) {
            if (file_exists($sqlFile)) {
                exec('mysql -u '.$db['username'].' -p'.$db['password'].' -h '.$db['host'].' '.$db['database'].' < '.$sqlFile);
            }
        }
    }
}
