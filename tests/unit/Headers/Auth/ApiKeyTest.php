<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\Headers\Auth;

use PHPUnit\Framework\TestCase;

class ApiKeyTest extends TestCase
{
    public function testAsString(): void
    {
        $this->assertEquals(
            'test',
            (
                new ApiKey("test")
            )->asString()
        );
    }
}
