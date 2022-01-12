<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\Headers\Auth;

use Otis22\VetmanagerToken\Credentials\AppName;
use Otis22\VetmanagerToken\Token\Concrete;
use PHPUnit\Framework\TestCase;

class ByTokenTest extends TestCase
{
    public function testAsKeyValue(): void
    {
        $this->assertTrue(
            in_array(
                'test-token',
                (
                    new ByToken(
                        new AppName('myapp'),
                        new Concrete('test-token')
                    )
                )->asKeyValue()
            )
        );
    }
}
