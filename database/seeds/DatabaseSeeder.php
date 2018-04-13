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
    	$this->call(BuildingCategoriesTableSeeder::class);
		$this->call(SpaceCategoriesTableSeeder::class);
		$this->call(AssessmentTypesTableSeeder::class);
		$this->call(BuildingTypesTableSeeder::class);
		$this->call(EnergyLabelsTableSeeder::class);
	    $this->call(ServiceTypesTableSeeder::class);
	    $this->call(RoofTypesTableSeeder::class);
	    $this->call(CooperationsTableSeeder::class);
	    $this->call(UsersTableSeeder::class);
	    $this->call(BuildingsTableSeeder::class);
	    $this->call(StepsTableSeeder::class);
	    $this->call(MeasureCategoriesTableSeeder::class);
	    $this->call(MeasuresTableSeeder::class);
	    $this->call(ExampleBuildingsTableSeeder::class);
	    $this->call(InterestsTableSeeder::class);
	    $this->call(InsulatingGlazingsTableSeeder::class);
		$this->call(ElementsValuesTableSeeder::class);
	    $this->call(MotivationsTableSeeder::class);
		$this->call(BuildingTypeElementMaxSavingTableSeeder::class);
	    $this->call(BuildingHeatingsTableSeeder::class);
		$this->call(MeasureApplicationsTableSeeder::class);
		$this->call(FacadeSurfacesTableSeeder::class);
		$this->call(FacadeDamagedPaintworksTableSeeder::class);
		$this->call(FacadePlasteredSurfacesTableSeeder::class);
		$this->call(PriceIndexingsTableSeeder::class);
		$this->call(KeyFigureTemperaturesTableSeeder::class);
		$this->call(PaintworkStatusesTableSeeder::class);
		$this->call(WoodRotStatusesTableSeeder::class);
		$this->call(CrawlspaceAccessesTableSeeder::class);
		$this->call(RoofTileStatusesTableSeeder::class);
	    $this->call(ServiceValuesTableSeeder::class);
	    $this->call(PvPanelOrientationsTableSeeder::class);
	    $this->call(PvPanelLocationFactorsTableSeeder::class);
	    $this->call(ComfortLevelTapWatersTableSeeder::class);
		$this->call(HeaterSpecificationsTableSeeder::class);
		$this->call(HeaterComponentCostsTableSeeder::class);

		// Depending on earlier seeded data
	    $this->call(KeyFigureBoilerEfficienciesTableSeeder::class);
	    $this->call(PvPanelYieldsTableSeeder::class);
		$this->call(KeyFigureConsumptionTapWatersTableSeeder::class);


		// New
		$this->call(HeatingAgesTableSeeder::class);

        $this->call(SolarWaterHeatersTableSeeder::class);
        $this->call(PresentWindowsTableSeeder::class);
        $this->call(PresentShowerWtwsTableSeeder::class);
        $this->call(InterestedToExecuteMeasuresTableSeeder::class);
        $this->call(ComfortComplaintsTableSeeder::class);
        $this->call(ExperienceAirQualityInHomesTableSeeder::class);
        $this->call(SufferFromsTableSeeder::class);
        $this->call(PresentHeatPumpsTableSeeder::class);


	    $this->call(VentilationsTableSeeder::class);
    }
}
