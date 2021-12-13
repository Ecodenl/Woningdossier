<?php

namespace App\Helpers;

class MediaHelper {
    const LOGO = 'logo';
    const BACKGROUND = 'background';

    public static function getTags(): array
    {
        return [
            self::LOGO => self::LOGO,
            self::BACKGROUND => self::BACKGROUND,
        ];
    }
}