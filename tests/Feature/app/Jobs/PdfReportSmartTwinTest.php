<?php

namespace Tests\Feature\app\Jobs;

use App\Enums\AdviceSource;
use App\Jobs\PdfReport;
use App\Models\Building;
use App\Models\FileStorage;
use App\Models\FileType;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\Scan;
use App\Models\SmartTwinPackage;
use App\Models\ToolQuestion;
use App\Models\User;
use App\Models\UserActionPlanAdvice;
use App\Services\ExecutedAdviceService;
use App\Services\ToolQuestionService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf;
use Tests\TestCase;

/**
 * The report once the woonplan is SmartTwin's shows what the woonplan shows, and leaves out what
 * rests on Hoomdossier's own calculation (decision 2026-10-15-smarttwin-rapport-na-omslagpunt).
 * Before the switch it is the report it always was.
 *
 * The HTML handed to mPDF is what is checked: the PDF itself is compressed.
 */
final class PdfReportSmartTwinTest extends TestCase
{
    use RefreshDatabase;

    public $seed = true;
    public $seeder = DatabaseSeeder::class;

    private User $user;
    private Building $building;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        config()->set('hoomdossier.services.smarttwin.enabled', true);
        Storage::fake('downloads');

        $this->user = User::first();
        $this->building = $this->user->building;
    }

    private function advice(string $short, string $category, float $to, ?AdviceSource $source = AdviceSource::SMART_TWIN): UserActionPlanAdvice
    {
        $measure = MeasureApplication::findByShort($short);
        $master = null;

        foreach ([InputSource::resident(), InputSource::coach(), InputSource::master()] as $inputSource) {
            $row = UserActionPlanAdvice::withoutEvents(fn () => UserActionPlanAdvice::withoutGlobalScopes()->forceCreate([
                'user_id'                         => $this->user->id,
                'input_source_id'                 => $inputSource->id,
                'user_action_plan_advisable_type' => MeasureApplication::class,
                'user_action_plan_advisable_id'   => $measure->id,
                'step_id'                         => $measure->step_id,
                'costs'                           => ['from' => null, 'to' => $to],
                'savings_money'                   => 123,
                'category'                        => $category,
                'visible'                         => true,
                'source'                          => $source?->value,
            ]));
            $master = $row;
        }

        return $master;
    }

    private function switchToSmartTwin(): void
    {
        $this->advice('cavity-wall-insulation', 'to-do', 1622.65);
        $this->advice('floor-insulation', 'later', 2450);
        $executed = $this->advice('roof-insulation-pitched-inside', 'later', 3000);
        app(ExecutedAdviceService::class)->markExecuted($executed, Carbon::parse('2026-09-01'), 'Dak geïsoleerd');

        SmartTwinPackage::create([
            'building_id'                   => $this->building->id,
            'input_source_id'               => InputSource::coach()->id,
            'measure_application_ids'       => [MeasureApplication::findByShort('cavity-wall-insulation')->id],
            'current_result'                => ['energyLabel' => 'C', 'energyConsumptionGas' => 1500, 'energyConsumptionElectricity' => 2500],
            'scenario_result'               => ['energyLabel' => 'A+++', 'energyConsumptionGas' => 400, 'energyConsumptionElectricity' => 2600],
            'energy_cost_savings_per_month' => 50,
            'total_subsidy'                 => 1000,
            'subsidies'                     => [['name' => 'ISDE', 'amount' => 1000]],
            'imported_at'                   => Carbon::parse('2026-10-14'),
        ]);
    }

    /**
     * Runs the report and returns the HTML it would have turned into a PDF.
     */
    private function reportHtml(): string
    {
        $html = '';
        LaravelMpdf::shouldReceive('loadView')->once()->andReturnUsing(function (string $view, array $data) use (&$html) {
            $html = view($view, $data)->render();

            return new class {
                public function output(): string
                {
                    return 'pdf';
                }
            };
        });

        $fileType = FileType::findByShort('pdf-report');
        $fileStorage = FileStorage::create([
            'building_id'     => $this->building->id,
            'cooperation_id'  => $this->user->cooperation->id,
            'input_source_id' => InputSource::resident()->id,
            'file_type_id'    => $fileType->id,
            'filename'        => 'report.pdf',
        ]);

        (new PdfReport($this->user, $fileType, $fileStorage, Scan::findByShort(Scan::QUICK)))->handle();

        // mPDF's own markup aside, collapse whitespace so assertions read naturally.
        return (string) preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($html)));
    }

    public function test_after_the_switch_the_woonplan_page_is_the_woonplans(): void
    {
        $this->switchToSmartTwin();

        $html = $this->reportHtml();

        // P1: "Gekozen pakket" with the card's amount in whole euros, not on steps of five.
        $this->assertStringContainsString(__('cooperation/frontend/tool.my-plan.package-results.title'), $html);
        $this->assertStringContainsString('€ 1.623', $html);
        $this->assertStringNotContainsString('€ 1.625', $html);
        $this->assertStringContainsString('€ 600 ' . __('cooperation/frontend/tool.my-plan.package-results.per-year'), $html);
        $this->assertStringContainsString('€ 1.000 (ISDE)', $html);
        $this->assertStringContainsString('C → A+++', $html);
        // "Later uitvoeren" with its costs, "In orde" with what was carried out.
        $this->assertStringContainsString('€ 2.450', $html);
        $this->assertStringContainsString('Uitgevoerd op 01-09-2026', $html);
        $this->assertStringContainsString('Dak geïsoleerd', $html);
        // No saving per measure.
        $this->assertStringNotContainsString(strip_tags(__('pdf/user-report.pages.action-plan.advices.savings')), $this->woonplanPageOf($html));
        // P2: SmartTwin's use now, not Hoomdossier's energy prices.
        $this->assertStringContainsString('1.500 m3', $html);
        $this->assertStringNotContainsString(strip_tags(__('pdf/user-report.pages.action-plan.usage.kengetallen')), $html);
    }

    public function test_after_the_switch_what_rests_on_hoomdossiers_calculation_is_left_out(): void
    {
        $this->switchToSmartTwin();

        $html = $this->reportHtml();

        // P3: no Verbeterjehuis subsidy. P4: SmartTwin's calculations.
        $this->assertStringNotContainsString(__('pdf/user-report.pages.info-page.subsidy.title'), $html);
        $this->assertStringContainsString('De berekeningen in dit woonplan zijn gemaakt door Smart Twin', $html);
        // P6: no expert scan pages.
        $this->assertStringNotContainsString(strip_tags(__('pdf/user-report.pages.expert-scan-answers.text.nl')), $html);
    }

    public function test_after_the_switch_the_dwelling_reads_as_gegevens_woning_bekijken(): void
    {
        $this->switchToSmartTwin();
        ToolQuestionService::init()
            ->toolQuestion(ToolQuestion::findByShort('wall-surface'))
            ->building($this->building)
            ->currentInputSource(InputSource::coach())
            ->save(80);
        $monument = ToolQuestion::findByShort('monument');
        ToolQuestionService::init()
            ->toolQuestion($monument)
            ->building($this->building)
            ->currentInputSource(InputSource::resident())
            ->save($monument->getQuestionValues()->first()['value']);

        $html = $this->reportHtml();

        $this->assertStringContainsString(__('cooperation/frontend/tool.my-plan.house-data.state'), $html);
        $this->assertStringContainsString('Geveloppervlakte van de woning', $html);
        $this->assertStringContainsString(ucfirst(trim($monument->name)), $html);
        // The steps SmartTwin takes over are not summarised as well (a question SmartTwin does not
        // fill would be there); Woonwensen still is.
        $this->assertStringNotContainsString(trim(ToolQuestion::findByShort('ventilation-type')->name), $html);
        $this->assertStringContainsString(\App\Models\Step::findByShort('living-requirements')->name, $html);
    }

    public function test_mpdf_makes_the_report_after_the_switch(): void
    {
        $this->switchToSmartTwin();
        $fileType = FileType::findByShort('pdf-report');
        $fileStorage = FileStorage::create([
            'building_id'     => $this->building->id,
            'cooperation_id'  => $this->user->cooperation->id,
            'input_source_id' => InputSource::resident()->id,
            'file_type_id'    => $fileType->id,
            'filename'        => 'report.pdf',
        ]);

        (new PdfReport($this->user, $fileType, $fileStorage, Scan::findByShort(Scan::QUICK)))->handle();

        $this->assertStringStartsWith('%PDF', (string) Storage::disk('downloads')->get('report.pdf'));
    }

    public function test_before_the_switch_the_report_is_as_it_was(): void
    {
        $this->advice('cavity-wall-insulation', 'to-do', 1622.65, null);

        $html = $this->reportHtml();

        $this->assertStringContainsString(strip_tags(__('pdf/user-report.pages.action-plan.usage.kengetallen')), $html);
        $this->assertStringContainsString(strip_tags(__('pdf/user-report.pages.action-plan.advices.savings')), $html);
        // The woonplan's own prices on steps of five, as on its cards.
        $this->assertStringContainsString('Spouwmuurisolatie 1.625', $this->woonplanPageOf($html));
        $this->assertStringNotContainsString(__('cooperation/frontend/tool.my-plan.package-results.title'), $html);
        $this->assertStringNotContainsString('De berekeningen in dit woonplan zijn gemaakt door Smart Twin', $html);
        // Every step summarised, also those SmartTwin takes over once it is on.
        $this->assertStringContainsString(trim(ToolQuestion::findByShort('ventilation-type')->name), $html);
    }

    /**
     * The woonplan page alone: the small measures page further on also has a savings column.
     */
    private function woonplanPageOf(string $html): string
    {
        $start = strpos($html, strip_tags(__('pdf/user-report.pages.action-plan.title')));
        $end = strpos($html, __('pdf/user-report.pages.info-page.calculations-are-indicative.title'));

        return substr($html, (int) $start, (int) $end - (int) $start);
    }
}
