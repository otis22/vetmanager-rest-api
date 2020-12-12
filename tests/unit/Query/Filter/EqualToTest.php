<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\Query\Filter;

use Otis22\VetmanagerRestApi\Model\Property;
use Otis22\VetmanagerRestApi\Query\Filter\Value\StringValue;
use PHPUnit\Framework\TestCase;

class EqualToTest extends TestCase
{

    public function testAsKeyValue(): void
    {
        $this->assertEquals(
            [
                'property' => 'test',
                'value' => 'test'
            ],
            (
                new EqualTo(
                    new Property('test'),
                    new StringValue('test')
                )
            )->asKeyValue()
        );
    }
}
