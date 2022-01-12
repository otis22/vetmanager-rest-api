<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\Query\Filter;

use Otis22\VetmanagerRestApi\Model\Property;
use Otis22\VetmanagerRestApi\Query\Filter\Value\StringValue;
use PHPUnit\Framework\TestCase;

class LessOrEqualThanTest extends TestCase
{
    public function testAsKeyValue(): void
    {
        $this->assertEquals(
            [
                'operator' => '<=',
                'property' => 'test',
                'value' => '1'
            ],
            (
            new LessOrEqualThan(
                new Property('test'),
                new StringValue('1')
            )
            )->asKeyValue()
        );
    }
}
