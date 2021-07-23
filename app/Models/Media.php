<?php

namespace App\Models;

class Media extends \Plank\Mediable\Media
{
    public function getUrl(): string
    {
        return parent::getUrl();
    }
}