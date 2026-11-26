<?php

namespace Tests\Feature\app\View;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class AlertComponentTest extends TestCase
{
    use RefreshDatabase;

    public bool $seed = true;
    public string $seeder = DatabaseSeeder::class;

    public static function colourProvider(): array
    {
        return [
            'success' => ['green', 'success'],
            'warning' => ['yellow', 'warning'],
            'error' => ['red', 'error'],
            'info (blue-900)' => ['blue-900', 'info'],
            'info (blue)' => ['blue', 'info'],
        ];
    }

    #[DataProvider('colourProvider')]
    public function test_alert_uses_the_colours_of_its_type(string $colour, string $type): void
    {
        $view = $this->blade(
            "@component('cooperation.layouts.components.alert', ['color' => \$colour]) Message @endcomponent",
            ['colour' => $colour]
        );

        $view->assertSee("alert-{$type}", false);
        $view->assertSee("border-alert-{$type}-border", false);
        $view->assertSee("bg-alert-{$type}-bg", false);
        $view->assertSee('text-blue ', false);
        if ($colour !== 'blue') {
            $view->assertDontSee("text-{$colour} ", false);
        }
        $view->assertSee('Message');
    }

    public function test_alert_without_colour_is_info(): void
    {
        $view = $this->blade("@component('cooperation.layouts.components.alert') Message @endcomponent");

        $view->assertSee('border-alert-info-border bg-alert-info-bg', false);
        $view->assertSee('text-blue ', false);
    }
}
