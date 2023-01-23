<?php

namespace App\Observers;

use App\Models\CooperationMeasureApplication;

class CooperationMeasureApplicationObserver
{
    public function saving(CooperationMeasureApplication $measureApplication)
    {
        $translations = $measureApplication->getTranslations('info');

        foreach ($translations as $locale => $data) {
            $translations[$locale] = strip_tags($data);
        }

        $measureApplication->info = $translations;
    }
}
