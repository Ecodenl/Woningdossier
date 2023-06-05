<?php

namespace App\Services\Kengetallen\Resolvers;

abstract class KengetallenDefiner
{
    public array $context;

    public abstract function get(string $kengetallenCode);

    public function context(array $context): self
    {
        $this->context = $context;

        return $this;
    }
}