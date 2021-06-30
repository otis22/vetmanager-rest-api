<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\Query\Filter;

use Otis22\VetmanagerRestApi\Model\Property;
use Otis22\VetmanagerRestApi\Query\Filter\Value\ArrayValue;
use PHPUnit\Framework\TestCase;

class NotInArrayTest extends TestCase
{
    public function testAsKeyValue(): void
    {
        $this->assertEquals(
            [
                'operator' => 'not in',
                'property' => 'test',
                'value' => [1,2,3]
            ],
            (
                new NotInArray(
                    new Property('test'),
                    new ArrayValue([1, 2, 3])
                )
            )->asKeyValue()
        );
    }
}
