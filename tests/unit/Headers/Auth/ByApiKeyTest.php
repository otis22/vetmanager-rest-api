<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\Headers\Auth;

use PHPUnit\Framework\TestCase;

class ByApiKeyTest extends TestCase
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
