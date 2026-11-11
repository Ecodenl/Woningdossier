<?php

namespace Tests\Feature\app\Console\Commands\Upgrade;

use App\Console\Commands\Upgrade\RemoveInsulationCentimeters;
use App\Models\Element;
use App\Models\ToolQuestion;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * The seeder already has the new names, so these tests put the old ones back first, as on a
 * database deployed before them, and let the command and deploy:run take them out again.
 */
final class RemoveInsulationCentimetersTest extends TestCase
{
    use RefreshDatabase;

    public $seed = true;
    public $seeder = DatabaseSeeder::class;

    private const COMMAND = 'upgrade:remove-insulation-centimeters';

    /** @return array<string, array<int, string>> The Dutch names of the options, by element. */
    private function names(): array
    {
        $names = [];

        foreach (RemoveInsulationCentimeters::ELEMENTS as $short) {
            $names[$short] = DB::table('element_values')
                ->where('element_id', Element::findByShort($short)->id)
                ->orderBy('order')
                ->get()
                ->map(fn ($row) => json_decode($row->value, true)['nl'])
                ->all();
        }

        return $names;
    }

    private function putOldNamesBack(): void
    {
        $old = array_flip(RemoveInsulationCentimeters::NAMES);

        foreach (RemoveInsulationCentimeters::ELEMENTS as $short) {
            $rows = DB::table('element_values')->where('element_id', Element::findByShort($short)->id)->get();

            foreach ($rows as $row) {
                $value = json_decode($row->value, true);

                if (isset($old[$value['nl']])) {
                    $value['nl'] = $old[$value['nl']];
                    DB::table('element_values')->where('id', $row->id)->update(['value' => json_encode($value)]);
                }
            }
        }
    }

    public function test_the_seeder_has_no_centimetres_in_the_insulation_options(): void
    {
        foreach ($this->names() as $short => $names) {
            $this->assertContains('Matige isolatie', $names, $short);
            $this->assertContains('Goede isolatie', $names, $short);
            $this->assertContains('Zeer goede isolatie', $names, $short);
            $this->assertEmpty(array_filter($names, fn ($name) => str_contains($name, 'cm')), $short);
        }
    }

    public function test_it_renames_what_the_seeder_would_and_nothing_twice(): void
    {
        $seeded = $this->names();
        $this->putOldNamesBack();
        $old = $this->names();
        $this->assertNotEquals($seeded, $old);

        $this->artisan(self::COMMAND, ['--dry-run' => true])->assertSuccessful();
        $this->assertSame($old, $this->names());

        $this->artisan(self::COMMAND)->expectsOutput('Done. Renamed 9 option(s).')->assertSuccessful();
        $this->assertSame($seeded, $this->names());

        $this->artisan(self::COMMAND)->expectsOutput('Already done.')->assertSuccessful();
        $this->assertSame($seeded, $this->names());
    }

    public function test_a_name_changed_since_and_other_languages_stay(): void
    {
        $this->putOldNamesBack();
        $elementId = Element::findByShort('wall-insulation')->id;
        $rows = DB::table('element_values')->where('element_id', $elementId)->get()
            ->keyBy(fn ($row) => json_decode($row->value, true)['nl']);

        $moderate = $rows['Matige isolatie (tot 8 cm isolatie)'];
        DB::table('element_values')->where('id', $moderate->id)
            ->update(['value' => json_encode(['nl' => 'Matige isolatie (tot 8 cm isolatie)', 'en' => 'Moderate insulation (up to 8 cm)'])]);
        $good = $rows['Goede isolatie (8 tot 20 cm isolatie)'];
        DB::table('element_values')->where('id', $good->id)
            ->update(['value' => json_encode(['nl' => 'Goed geïsoleerd'])]);

        $this->artisan(self::COMMAND)->assertSuccessful();

        // A JSON column keeps its own key order.
        $this->assertEquals(
            ['nl' => 'Matige isolatie', 'en' => 'Moderate insulation (up to 8 cm)'],
            json_decode(DB::table('element_values')->where('id', $moderate->id)->value('value'), true),
        );
        $this->assertSame(
            ['nl' => 'Goed geïsoleerd'],
            json_decode(DB::table('element_values')->where('id', $good->id)->value('value'), true),
        );
    }

    public function test_deploy_renames_them_in_the_questions(): void
    {
        $this->putOldNamesBack();

        $this->artisan('deploy:run')->assertSuccessful();

        foreach (['current-wall-insulation', 'current-floor-insulation', 'current-roof-insulation'] as $short) {
            $names = ToolQuestion::findByShort($short)->getQuestionValues(true)->pluck('name')->all();

            $this->assertContains('Matige isolatie', $names, $short);
            $this->assertNotContains('Matige isolatie (tot 8 cm isolatie)', $names, $short);
        }
    }
}
