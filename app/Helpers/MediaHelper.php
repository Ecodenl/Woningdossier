<?php

namespace App\Helpers;

use App\Models\Building;
use App\Models\Cooperation;

class MediaHelper {
    const LOGO = 'logo';
    const BACKGROUND = 'background';
    const FILE = 'file';

    public static function getFillableTagsForClass(?string $class = null): array
    {
        switch ($class) {
            case Cooperation::class:
                return [
                    self::LOGO => self::LOGO,
                    self::BACKGROUND => self::BACKGROUND,
                ];

            case Building::class:
                return [
                    self::FILE => self::FILE,
                ];

            default:
                return [];
        }
    }

    public static function getImageMimes(bool $asArray = false)
    {
        $mimes = config('hoomdossier.media.accepted_image_mimes');
        return $asArray ? explode(',', $mimes) : $mimes;
    }

    public static function getFileMimes(bool $asArray = false)
    {
        $mimes = config('hoomdossier.media.accepted_file_mimes');
        return $asArray ? explode(',', $mimes) : $mimes;
    }

    public static function getAllMimes(): string
    {
        // We assume the developers aren't retarded and don't leave trailing commas
        return static::getFileMimes() . ',' . static::getImageMimes();
    }

    public static function getMaxFileSize()
    {
        return config('hoomdossier.media.max_size');
    }
}