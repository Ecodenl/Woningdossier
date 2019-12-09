<?php

namespace App\Observers;

use App\Models\Translation;

class TranslationObserver
{

    public function created(Translation $translation)
    {
        // cache it
        \App\Helpers\Cache\Translation::getTranslations($translation);
    }

    public function updated(Translation $translation)
    {
        // recache
        \App\Helpers\Cache\Translation::wipe($translation);
        \App\Helpers\Cache\Translation::getTranslations($translation);
    }

    public function deleted(Translation $translation)
    {
        \App\Helpers\Cache\Translation::wipe($translation);
    }
}