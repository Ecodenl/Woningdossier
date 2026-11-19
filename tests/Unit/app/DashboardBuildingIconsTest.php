<?php

namespace Tests\Unit\app;

use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Icons are CSS classes that point at a file in public/icons. A typo in either shows nothing at all
 * rather than an error, so check that the dashboard's icons resolve to a file that is there.
 */
final class DashboardBuildingIconsTest extends TestCase
{
    public static function iconProvider(): array
    {
        return [
            'address' => ['icon-address-house'],
            'surface' => ['icon-surface-m2'],
            'energy label' => ['icon-energy-label'],
        ];
    }

    #[DataProvider('iconProvider')]
    public function test_the_icon_class_points_at_an_existing_svg(string $class): void
    {
        $css = File::get(resource_path('css/base/components/icons.css'));

        $this->assertSame(
            1,
            preg_match('/\.' . preg_quote($class, '/') . '\s*\{[^}]*url\(\'\/icons\/([^\']+)\'\)/', $css, $matches),
            "No rule with an icon url for .{$class}",
        );
        $this->assertFileExists(public_path("icons/{$matches[1]}"));
    }
}
