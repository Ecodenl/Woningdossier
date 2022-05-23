<?php

namespace App\Services\Cloning\Cloners;

use App\Models\InputSource;

abstract class Cloner {

    protected array $data;

    protected InputSource $inputSource;

    public function __construct(array $data, InputSource $inputSource)
    {
        $this->data = $data;
        $this->inputSource = $inputSource;
    }
}