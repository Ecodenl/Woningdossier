<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
	    $this->call(TitlesTableSeeder::class);
	    $this->call(IndustriesTableSeeder::class);
	    $this->call(OrganisationTypesTableSeeder::class);
	    $this->call(PersonTypesTableSeeder::class);
	    $this->call(OccupationsTableSeeder::class);
	    $this->call(ReasonsTableSeeder::class);
		$this->call(SourcesTableSeeder::class);
		$this->call(BuildingTypesTableSeeder::class);
		$this->call(EnergyLabelsTableSeeder::class);
		$this->call(RegistrationStatusesTableSeeder::class);
		$this->call(TaskTypesTableSeeder::class);
		$this->call(ServiceTypesTableSeeder::class);
		$this->call(TaskPropertiesTableSeeder::class);
		$this->call(AssessmentTypesTableSeeder::class);
		$this->call(BuildingCategoriesTableSeeder::class);
		$this->call(SpaceCategoriesTableSeeder::class);
		$this->call(MeasuresTableSeeder::class);
		$this->call(MeasureCategoriesTableSeeder::class);
		$this->call(CooperationsTableSeeder::class);
    }
}
