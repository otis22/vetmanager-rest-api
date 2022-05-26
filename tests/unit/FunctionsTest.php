<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi;

use PHPUnit\Framework\TestCase;

use function Otis22\VetmanagerRestApi\byApiKey;
use function Otis22\VetmanagerRestApi\byToken;
use function Otis22\VetmanagerRestApi\uri;

class FunctionsTest extends TestCase
{
    public function testByApiKey(): void
    {
        $this->assertArrayHasKey(
            'X-REST-API-KEY',
            byApiKey('test')->asKeyValue()
        );
    }

    public function testByToken(): void
    {
        $this->assertArrayHasKey(
            'X-APP-NAME',
            byToken('app_name', 'token')->asKeyValue()
        );
    }

    public function testByServiceName(): void
    {
        $this->assertArrayHasKey(
            "X-SERVICE-NAME",
            byServiceApiKey('name', 'key')->asKeyValue()
        );
    }

    public function testUriWithoutId(): void
    {
        $this->assertStringContainsString(
            '/invoice',
            uri('invoice')->asString()
        );
    }

    public function testUriWithtId(): void
    {
        $this->assertStringContainsString(
            '/invoice/1',
            uri('invoice', 1)->asString()
        );
    }
}
