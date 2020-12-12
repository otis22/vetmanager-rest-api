<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\Query\Filter\Value;

use PHPUnit\Framework\TestCase;

class ArrayValueTest extends TestCase
{
    public function testAsString(): void
    {
        $this->assertEquals(
            '[1,2,3]',
            (
                new ArrayValue([1, 2 ,3])
            )->asString()
        );
    }
}
