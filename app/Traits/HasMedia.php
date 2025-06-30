<?php

namespace App\Traits;

use Plank\Mediable\Mediable;

trait HasMedia
{
    use Mediable;

    /**
     * NOTE: The provided URL may be 403 forbidden due to disk visibility.
     * Get the URL of the first media.
     *
     * @throws \Plank\Mediable\Exceptions\MediaUrlException
     */
    public function firstMediaUrl(string|array $tags, bool $matchAll = false): ?string
    {
        return $this->firstMedia($tags, $matchAll)?->getUrl();
    }
}
