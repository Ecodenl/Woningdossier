<?php

namespace Tests\Unit\app;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * Text is coloured `text-blue`, never `text-blue-500`: the two are different colours in
 * tailwind.config.js. Borders do use `border-blue-500`; this only concerns the text colour.
 */
final class TextColourTest extends TestCase
{
    public function test_no_view_or_stylesheet_colours_text_with_blue_500(): void
    {
        $offenders = collect(File::allFiles(resource_path()))
            ->filter(fn (\SplFileInfo $file) => in_array($file->getExtension(), ['php', 'css', 'js', 'vue'], true))
            ->filter(fn (\SplFileInfo $file) => str_contains(File::get($file->getPathname()), 'text-blue-500'))
            ->map(fn (\SplFileInfo $file) => str_replace(base_path() . '/', '', $file->getPathname()))
            ->values()
            ->all();

        $this->assertSame([], $offenders);
    }
}
