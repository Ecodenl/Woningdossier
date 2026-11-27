<?php

namespace Tests\Feature\database\Seeders;

use App\Models\MeasureApplication;
use App\Models\ToolQuestion;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\MeasureApplicationsTableSeeder;
use Database\Seeders\ToolQuestionsTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * deploy:run reseeds the measures and the questions. These tests put the icons a database had before
 * HD-24 back, and check that reseeding gives the supplied icons, without overwriting an icon the super
 * admin chose.
 */
final class SuppliedIconsSeederTest extends TestCase
{
    use RefreshDatabase;

    public $seed = true;
    public $seeder = DatabaseSeeder::class;

    /** By measure short: the icon it had before, and the one it has now. */
    public static function measureProvider(): array
    {
        return [
            'lighting' => ['save-energy-with-light', 'icon-tools', 'icon-small-measure-lighting'],
            'equipment' => ['energy-efficient-equipment', 'icon-tools', 'icon-small-measure-equipment'],
            'installations' => ['energy-efficient-installations', 'icon-tools', 'icon-small-measure-installations'],
            'crack sealing' => ['save-energy-with-crack-sealing', 'icon-tools', 'icon-small-measure-crack-sealing'],
            'radiators' => ['improve-radiators', 'icon-tools', 'icon-small-measure-radiators'],
            'heating installation' => ['improve-heating-installations', 'icon-tools', 'icon-small-measure-heating-installation'],
            'hot tap water' => ['save-energy-with-warm-tap-water', 'icon-tools', 'icon-small-measure-hot-tap-water'],
            'general' => ['general', 'icon-tools', 'icon-small-measure-general'],
            'heat pump boiler' => ['heat-pump-boiler-place-replace', 'icon-placeholder', 'icon-heat-pump-boiler'],
            'vacuum glass' => ['vacuum-glass-only', 'icon-glass-hr-dp', 'icon-glass-vacuum'],
            'district heating' => ['district-heating-connect', 'icon-district-heating', 'icon-district-heating-city'],
        ];
    }

    private function setMeasureIcon(string $short, string $icon): void
    {
        $measure = MeasureApplication::findByShort($short);
        $measure->update(['configurations' => array_merge($measure->configurations, ['icon' => $icon])]);
    }

    #[DataProvider('measureProvider')]
    public function test_reseeding_replaces_the_icon_the_measure_had_before(string $short, string $old, string $new): void
    {
        $this->setMeasureIcon($short, $old);

        $this->seed(MeasureApplicationsTableSeeder::class);

        $configurations = MeasureApplication::findByShort($short)->configurations;
        $this->assertSame($new, $configurations['icon']);
        $this->assertArrayNotHasKey('replaces_icons', $configurations);
    }

    #[DataProvider('measureProvider')]
    public function test_reseeding_keeps_an_icon_the_super_admin_chose(string $short, string $old, string $new): void
    {
        $this->assertNotSame($new, $old);
        $this->setMeasureIcon($short, 'icon-account-circle');

        $this->seed(MeasureApplicationsTableSeeder::class);

        $this->assertSame('icon-account-circle', MeasureApplication::findByShort($short)->configurations['icon']);
    }

    public static function optionProvider(): array
    {
        $options = [];
        foreach (['heat-source', 'new-heat-source'] as $question) {
            $options["{$question} infrared"] = [$question, 'infrared', 'icon-infrared-panels'];
            $options["{$question} district heating"] = [$question, 'district-heating', 'icon-district-heating-city'];
        }
        foreach (['heat-source-warm-tap-water', 'new-heat-source-warm-tap-water'] as $question) {
            $options["{$question} kitchen geyser"] = [$question, 'kitchen-geyser', 'icon-kitchen-geyser'];
            $options["{$question} electric boiler"] = [$question, 'electric-boiler', 'icon-electric-boiler'];
            $options["{$question} heat pump boiler"] = [$question, 'heat-pump-boiler', 'icon-heat-pump-boiler'];
            $options["{$question} district heating"] = [$question, 'district-heating', 'icon-district-heating-city'];
        }
        foreach (['boiler-setting-comfort-heat', 'new-boiler-setting-comfort-heat'] as $question) {
            $options["{$question} high"] = [$question, 'temp-high', 'icon-heating-temperature-high'];
            $options["{$question} 50"] = [$question, 'temp-50', 'icon-heating-temperature-50'];
            $options["{$question} low"] = [$question, 'temp-low', 'icon-heating-temperature-low'];
        }

        return $options;
    }

    #[DataProvider('optionProvider')]
    public function test_reseeding_gives_the_option_its_supplied_icon(string $question, string $option, string $icon): void
    {
        $value = ToolQuestion::findByShort($question)->toolQuestionCustomValues()->where('short', $option)->firstOrFail();
        $value->update(['extra' => array_merge($value->extra ?? [], ['icon' => 'icon-placeholder'])]);

        $this->seed(ToolQuestionsTableSeeder::class);

        $this->assertSame($icon, $value->fresh()->extra['icon']);
    }
}
