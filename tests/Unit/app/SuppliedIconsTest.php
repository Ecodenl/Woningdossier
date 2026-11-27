<?php

namespace Tests\Unit\app;

use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * The icons Econobis supplied for the small measures, the heaters, the heating temperatures and vacuum
 * glass (HD-24). An icon is a CSS class pointing at a file in public/icons; a typo in either shows an
 * empty spot rather than an error. The super admin's icon picker derives the class from the file name,
 * so the two have to match as well.
 */
final class SuppliedIconsTest extends TestCase
{
    public static function iconProvider(): array
    {
        return array_map(fn (string $class) => [$class], [
            'lighting' => 'icon-small-measure-lighting',
            'equipment' => 'icon-small-measure-equipment',
            'installations' => 'icon-small-measure-installations',
            'crack sealing' => 'icon-small-measure-crack-sealing',
            'radiators' => 'icon-small-measure-radiators',
            'heating installation' => 'icon-small-measure-heating-installation',
            'hot tap water' => 'icon-small-measure-hot-tap-water',
            'general' => 'icon-small-measure-general',
            'infrared panels' => 'icon-infrared-panels',
            'district heating' => 'icon-district-heating-city',
            'kitchen geyser' => 'icon-kitchen-geyser',
            'electric boiler' => 'icon-electric-boiler',
            'heat pump boiler' => 'icon-heat-pump-boiler',
            'high temperature' => 'icon-heating-temperature-high',
            '50 degrees' => 'icon-heating-temperature-50',
            'low temperature' => 'icon-heating-temperature-low',
            'vacuum glass' => 'icon-glass-vacuum',
        ]);
    }

    #[DataProvider('iconProvider')]
    public function test_the_icon_class_points_at_the_svg_of_the_same_name(string $class): void
    {
        $css = File::get(resource_path('css/base/components/icons.css'));

        $this->assertSame(
            1,
            preg_match('/\.' . preg_quote($class, '/') . '\s*\{[^}]*url\(\'\/icons\/([^\']+)\'\)/', $css, $matches),
            "No rule with an icon url for .{$class}",
        );
        $this->assertSame(substr($class, strlen('icon-')) . '.svg', $matches[1]);
        $this->assertFileExists(public_path("icons/{$matches[1]}"));
    }

    #[DataProvider('iconProvider')]
    public function test_the_svg_is_a_plain_vector_drawing(string $class): void
    {
        $svg = File::get(public_path('icons/' . substr($class, strlen('icon-')) . '.svg'));

        $this->assertNotFalse(simplexml_load_string($svg), 'Not valid XML');
        // The supplied files carried a large signed manifest; it adds nothing to the drawing.
        $this->assertStringNotContainsString('<metadata', $svg);
        $this->assertStringNotContainsString('<script', $svg);
        $this->assertStringNotContainsString('<image', $svg);
    }
}
