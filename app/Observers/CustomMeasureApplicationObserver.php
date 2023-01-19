<?php

namespace App\Observers;

use App\Models\CustomMeasureApplication;

class CustomMeasureApplicationObserver
{
    public function saving(CustomMeasureApplication $measureApplication)
    {
        $translations = $measureApplication->getTranslations('info');

        foreach ($translations as $locale => $data) {
            $translations[$locale] = strip_tags($data);
        }

        $measureApplication->info = $translations;
    }
}
