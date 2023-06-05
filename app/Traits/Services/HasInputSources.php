<?php

namespace App\Traits\Services;

use App\Models\InputSource;

trait HasInputSources
{
    public ?InputSource $inputSource = null;

    public function forInputSource(InputSource $inputSource): self
    {
        $this->inputSource = $inputSource;
        return $this;
    }

    public function masterInputSource(): InputSource
    {
        return InputSource::master();
    }
}