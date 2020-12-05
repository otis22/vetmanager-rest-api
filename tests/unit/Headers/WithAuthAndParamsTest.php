<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\Headers;

use Otis22\VetmanagerRestApi\Headers\Auth\ApiKey;
use Otis22\VetmanagerRestApi\Headers\Auth\ByApiKey;
use PHPUnit\Framework\TestCase;

class WithAuthAndParamsTest extends TestCase
{
    public function testAsKeyValue(): void
    {
        $this->assertTrue(
            in_array(
                'test-param-value',
                (
                    new WithAuthAndParams(
                        new ByApiKey(
                            new ApiKey('test-key')
                        ),
                        [
                            'header-param' => 'test-param-value'
                        ]
                    )
                )->asKeyValue()
            )
        );
    }
}
