<?php

namespace App\Services;

class AddressService
{
    /**
     * Normalizes a zipcode to a Dutch zipcode format optionally with a space.
     *
     * @param bool $withSpace Defaults to false
     */
    public function normalizeZipcode(?string $zip, bool $withSpace = false): ?string
    {
        if (empty($zip)) {
            return $zip;
        }

        $zip = preg_replace('/\s+/', '', trim($zip));
        if (strlen($zip) > 4) {
            $space = $withSpace ? ' ' : '';

            return strtoupper(substr($zip, 0, 4) . $space . substr($zip, 4));
        }

        return strtoupper($zip);
    }
}
