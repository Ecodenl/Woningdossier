<?php

namespace Tests\Unit\app\Services\Verbeterjehuis\Payloads;

use App\Services\Verbeterjehuis\Payloads\Search;
use Tests\TestCase;

final class SearchTest extends TestCase
{
    public function test_prepare_defaults_missing_optional_fields(): void
    {
        $regulation = Search::init([
            [
                'Id' => '1',
                'Title' => 'Subsidy without intro',
                'Type' => 'subsidy',
                'TargetGroup' => 'Owner, Tenant',
                'Tags' => [],
            ],
        ])->all()->first();

        $this->assertSame('', $regulation['Intro']);
        $this->assertSame('', $regulation['Details']);
        $this->assertNull($regulation['ProviderUrl']);
        $this->assertSame(['Owner', 'Tenant'], $regulation['TargetGroup']);
    }

    public function test_prepare_keeps_provided_optional_fields(): void
    {
        $regulation = Search::init([
            [
                'Id' => '1',
                'Title' => 'Subsidy with intro',
                'Intro' => 'Intro text',
                'Details' => '<p>Details</p>',
                'ProviderUrl' => 'https://example.com',
                'Type' => 'subsidy',
                'TargetGroup' => 'Owner',
                'Tags' => [],
            ],
        ])->all()->first();

        $this->assertSame('Intro text', $regulation['Intro']);
        $this->assertSame('<p>Details</p>', $regulation['Details']);
        $this->assertSame('https://example.com', $regulation['ProviderUrl']);
    }
}
