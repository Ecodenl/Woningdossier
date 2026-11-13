<?php

namespace Tests\Feature\app\Models;

use App\Models\Building;
use App\Models\BuildingFeature;
use App\Models\EnergyLabel;
use App\Models\InputSource;
use App\Models\ToolQuestion;
use App\Services\EpOnline\EnergyLabelService;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\EnergyLabelsTableSeeder;
use Database\Seeders\ToolQuestionsTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

/**
 * The labels above A (A+ to A++++), official since NTA 8800, are labels of their own: as EP-Online
 * and SmartTwin give them, selectable in the question, shown with the icon of A.
 */
final class EnergyLabelTest extends TestCase
{
    use RefreshDatabase;

    public bool $seed = true;
    public string $seeder = DatabaseSeeder::class;

    public function test_the_labels_above_a_are_listed_after_a_in_the_question(): void
    {
        $names = ToolQuestion::findByShort('energy-label')->getQuestionValues()->pluck('name')->all();

        $this->assertSame(['G', 'F', 'E', 'D', 'C', 'B', 'A', 'A+', 'A++', 'A+++', 'A++++', '?', 'X'], $names);
    }

    public function test_a_label_above_a_takes_the_icon_of_a_and_a_caption(): void
    {
        $values = ToolQuestion::findByShort('energy-label')->getQuestionValues()->keyBy('name');

        $this->assertSame(['icon' => 'icon-label-a', 'caption' => 'A+++'], $values['A+++']['extra']);
        $this->assertSame(['icon' => 'icon-label-a'], $values['A']['extra']);
    }

    public function test_seeding_an_existing_database_again_adds_the_labels_without_moving_the_others(): void
    {
        // The database as it was: nine labels, and the question offering those nine.
        $before = EnergyLabel::whereNotIn('name', ['A+', 'A++', 'A+++', 'A++++'])->pluck('id', 'name')->all();
        EnergyLabel::whereIn('name', ['A+', 'A++', 'A+++', 'A++++'])->delete();
        $question = ToolQuestion::findByShort('energy-label');
        DB::table('tool_question_valuables')->where('tool_question_id', $question->id)->delete();
        $this->seed(ToolQuestionsTableSeeder::class);
        $this->assertCount(9, $question->getQuestionValues());

        // What deploy:run does.
        $this->seed(EnergyLabelsTableSeeder::class);
        $this->seed(ToolQuestionsTableSeeder::class);

        $this->assertSame($before, EnergyLabel::whereIn('name', array_keys($before))->pluck('id', 'name')->all());
        $this->assertSame(
            ['G', 'F', 'E', 'D', 'C', 'B', 'A', 'A+', 'A++', 'A+++', 'A++++', '?', 'X'],
            $question->getQuestionValues()->pluck('name')->all(),
        );
        $this->assertSame(13, DB::table('tool_question_valuables')->where('tool_question_id', $question->id)->count());
    }

    public function test_the_icon_of_a_label(): void
    {
        $this->assertSame('icon-label-a', EnergyLabel::iconClassFor('A++++'));
        $this->assertSame('icon-label-a', EnergyLabel::iconClassFor('A'));
        $this->assertSame('icon-label-c', EnergyLabel::iconClassFor('C'));
        $this->assertSame('icon-label-none', EnergyLabel::iconClassFor('X'));
        $this->assertSame('icon-label-unknown', EnergyLabel::iconClassFor('?'));
        $this->assertSame('icon-label-unknown', EnergyLabel::iconClassFor(null));
        $this->assertSame('icon-label-a', EnergyLabel::fromName('A+')->iconClass());
    }

    public function test_a_label_is_found_by_its_name_or_is_none(): void
    {
        $this->assertSame('A+++', EnergyLabel::fromName('a+++')->name);
        $this->assertSame('C', EnergyLabel::fromName(' C ')->name);
        $this->assertSame('X', EnergyLabel::fromName('Z')->name);
        $this->assertSame('X', EnergyLabel::fromName(null)->name);
    }

    public function test_ep_online_keeps_a_label_above_a(): void
    {
        $building = Building::factory()->create();

        $service = Mockery::mock(EnergyLabelService::class)->makePartial();
        $service->shouldReceive('getEnergyLabel')->andReturn('A+++');
        $service->forBuilding($building)->syncEnergyLabel();

        $external = BuildingFeature::withoutGlobalScopes()
            ->where('building_id', $building->id)
            ->where('input_source_id', InputSource::external()->id)
            ->first();

        $this->assertSame('A+++', EnergyLabel::find($external->energy_label_id)->name);
    }
}
