<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi;

use Otis22\VetmanagerRestApi\Headers;
use PHPUnit\Framework\TestCase;

use function Otis22\VetmanagerRestApi\byApiKey;
use function Otis22\VetmanagerRestApi\byToken;
use function Otis22\VetmanagerRestApi\uri;

class FunctionsTest extends TestCase
{
    public function testByApiKey(): void
    {
        $this->assertInstanceOf(
            Headers::class,
            byApiKey('test')
        );
    }

    public function testByToken(): void
    {
        $this->assertInstanceOf(
            Headers::class,
            byToken('app_name', 'token')
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
