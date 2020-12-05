<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\Headers;

use Otis22\VetmanagerRestApi\Headers\Auth\ApiKey;
use Otis22\VetmanagerRestApi\Headers\Auth\ByApiKey;
use PHPUnit\Framework\TestCase;

class WithAuthTest extends TestCase
{
    public function testAsKeyValue(): void
    {
        $this->assertTrue(
            in_array(
                'test-key',
                (
                    new ByApiKey(
                        new ApiKey('test-key')
                    )
                )->asKeyValue()
            )
        );
    }
}
