<?php

namespace App\Helpers;

use App\Models\Building;
use App\Models\Cooperation;

class MediaHelper {
    const LOGO = 'logo';
    const BACKGROUND = 'background';
    const GENERIC_FILE = 'generic-file';
    const GENERIC_IMAGE = 'generic-image';
    const REPORT = 'report';
    const QUOTATION = 'quotation';
    const INVOICE = 'invoice';
    const BILL = 'bill';

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
                    self::GENERIC_FILE => self::GENERIC_FILE,
                    self::GENERIC_IMAGE => self::GENERIC_IMAGE,
                    self::REPORT => self::REPORT,
                    self::QUOTATION => self::QUOTATION,
                    self::INVOICE => self::INVOICE,
                    self::BILL => self::BILL,
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