<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\Query;

use Otis22\VetmanagerRestApi\Model\Property;
use Otis22\VetmanagerRestApi\Query\Filter\EqualTo;
use Otis22\VetmanagerRestApi\Query\Filter\NotEqualTo;
use Otis22\VetmanagerRestApi\Query\Filter\Value\StringValue;
use PHPUnit\Framework\TestCase;

class FiltersTest extends TestCase
{
    public function testAsArray(): void
    {
        $this->assertEquals(
            [
                'filter' => '[{"property":"test","value":"test"},{"property":"test2","value":"test2","operator":"!="}]'
            ],
            (
                new Filters(
                    new EqualTo(
                        new Property('test'),
                        new StringValue('test')
                    ),
                    new NotEqualTo(
                        new Property('test2'),
                        new StringValue('test2')
                    )
                )
            )->asKeyValue()
        );
    }
}
