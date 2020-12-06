<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\URI;

use Otis22\VetmanagerRestApi\Model;
use PHPUnit\Framework\TestCase;

class WithIdTest extends TestCase
{

    public function testAsString(): void
    {
        $this->assertTrue(
            strpos(
                (
                    new WithId(
                        new Model('invoice'),
                        1
                    )
                )->asString(),
                'invoice/1'
            ) !== false
        );
    }
}
