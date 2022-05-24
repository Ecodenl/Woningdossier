<?php

namespace App\Services\Cloning\Cloners;

interface DataTransformer
{
    public function transFormCloneableData(): array;
}