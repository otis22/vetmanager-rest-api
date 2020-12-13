<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\Query;

use Otis22\VetmanagerRestApi\Model\Property;
use Otis22\VetmanagerRestApi\Query\Filter\EqualTo;
use Otis22\VetmanagerRestApi\Query\Filter\Value\StringValue;
use Otis22\VetmanagerRestApi\Query\Sort\AscBy;
use PHPUnit\Framework\TestCase;

class QueryTest extends TestCase
{
    public function testAsKeyValue(): void
    {
        $query = new Query(
            new Filters(
                new EqualTo(
                    new Property('test'),
                    new StringValue('test')
                )
            ),
            new Sorts(
                new AscBy(
                    new Property('test')
                )
            )
        );
        $this->assertArrayHasKey(
            'sort',
            $query->asKeyValue()
        );
        $this->assertArrayHasKey(
            'filter',
            $query->asKeyValue()
        );
    }
}
