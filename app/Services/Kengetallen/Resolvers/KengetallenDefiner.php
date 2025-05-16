<?php

namespace App\Services\Kengetallen\Resolvers;

abstract class KengetallenDefiner
{
    public array $context;

    abstract public function get(string $kengetallenCode);

    public function context(array $context): self
    {
        $this->context = $context;
        return $this;
    }
}
