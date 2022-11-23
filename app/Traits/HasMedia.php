<?php

namespace App\Traits;

use Plank\Mediable\Mediable;

trait HasMedia
{
    use Mediable;

    /**
     * Get the URL of the first media.
     *
     * @param $tags
     * @param bool $matchAll
     *
     * @return string|null
     * @throws \Plank\Mediable\Exceptions\MediaUrlException
     */
    public function firstMediaUrl($tags, bool $matchAll = false): ?string
    {
        return optional($this->firstMedia($tags, $matchAll))->getUrl();
    }
}