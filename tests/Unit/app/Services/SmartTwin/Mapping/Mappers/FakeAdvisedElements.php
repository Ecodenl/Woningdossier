<?php

namespace Tests\Unit\app\Services\SmartTwin\Mapping\Mappers;

use App\Services\SmartTwin\Mapping\Elements\AdvisedElements;
use App\Services\SmartTwin\Mapping\Elements\ElementScope;

/**
 * Hands back the scopes a test sets, so a mapper can be tested without coupling solutions to
 * measures in a database. AdvisedElementsTest covers the real lookup.
 */
final class FakeAdvisedElements extends AdvisedElements
{
    /** @var array<int, ElementScope> */
    public array $scopes = [];

    public function __construct(ElementScope ...$scopes)
    {
        $this->scopes = $scopes;
    }

    public function scopes(array $response): array
    {
        return $this->scopes;
    }
}
