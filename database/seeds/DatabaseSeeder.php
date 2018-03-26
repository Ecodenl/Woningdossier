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
	    $this->call(UsersTableSeeder::class);
		// New
		$this->call(HeatingAgesTableSeeder::class);
        $this->call(BuildingHeatingsTableSeeder::class);
        $this->call(MotivationsTableSeeder::class);
        $this->call(QualitiesTableSeeder::class);
        $this->call(SolarWaterHeatersTableSeeder::class);
        $this->call(PresentWindowsTableSeeder::class);
        $this->call(PresentShowerWtwsTableSeeder::class);
        $this->call(InterestedToExecuteMeasuresTableSeeder::class);
        $this->call(ComfortComplaintsTableSeeder::class);
        $this->call(ExperienceAirQualityInHomesTableSeeder::class);
        $this->call(SufferFromsTableSeeder::class);
        $this->call(PresentHeatPumpsTableSeeder::class);

        $this->call(ExampleBuildingsTableSeeder::class);
        $this->call(RoofTypesTableSeeder::class);
        $this->call(InterestsTableSeeder::class);
        $this->call(StepsTableSeeder::class);
        $this->call(VentilationsTableSeeder::class);
        $this->call(ComfortLevelTapWatersTableSeeder::class);
        $this->call(SurfacePaintedWallsTableSeeder::class);
        $this->call(WallNeedImpregnationsTableSeeder::class);
        $this->call(CrawlSpaceHeightsTableSeeder::class);

    }
}
