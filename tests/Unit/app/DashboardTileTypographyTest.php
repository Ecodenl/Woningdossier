<?php

namespace Tests\Unit\app;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * The house and coach tiles show their details alike: a small bold label above a larger bold value.
 * Both share one rule in the stylesheet, so neither tile can drift from the other.
 */
final class DashboardTileTypographyTest extends TestCase
{
    private function ruleFor(string $selector): string
    {
        $css = File::get(resource_path('css/base/components/dashboard.css'));

        $this->assertSame(1, preg_match('/' . preg_quote($selector, '/') . '\s*\{([^}]*)\}/', $css, $matches));

        return $matches[1];
    }

    public function test_labels_are_small_and_bold_in_both_tiles(): void
    {
        $rule = $this->ruleFor(".building-fact dt,\n.coach-fact dt");

        $this->assertStringContainsString('text-sm', $rule);
        $this->assertStringContainsString('font-bold', $rule);
    }

    public function test_values_are_bold_in_both_tiles_without_shrinking_to_the_label_size(): void
    {
        $rule = $this->ruleFor(".building-fact dd,\n.coach-fact dd");

        $this->assertStringContainsString('font-bold', $rule);
        $this->assertStringNotContainsString('text-sm', $rule);
    }
}
