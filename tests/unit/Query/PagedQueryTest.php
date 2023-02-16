<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\Query;

use Otis22\VetmanagerRestApi\Model\Property;
use Otis22\VetmanagerRestApi\Query\Sort\AscBy;
use PHPUnit\Framework\TestCase;

class PagedQueryTest extends TestCase
{
    public function testForGettingAll(): void
    {
        $this->assertInstanceOf(
            PagedQuery::class,
            PagedQuery::forGettingAll(new Query())
        );
    }

    public function testSortingRequired(): void
    {
        $this->expectException(\Exception::class);
        PagedQuery::forGettingAll(new Query())->asKeyValue();
    }

    public function testSortingAndNotSortRequired(): void
    {
        $this->expectException(\Exception::class);
        PagedQuery::forGettingAll(new Query(new Sorts()))->asKeyValue();
    }

    public function testZeroOffsetForFirstPage(): void
    {
        $this->assertEquals(
            0,
            PagedQuery::forGettingAll(
                new Query(
                    new Sorts(
                        new AscBy(
                            new Property('id')
                        )
                    )
                )
            )->asKeyValue()['offset']
        );
    }

    public function testOneNextPageWhereOffsetWillEqualsLimit(): void
    {
        $this->assertEquals(
            1,
            (
                new PagedQuery(
                    new Query(
                        new Sorts(
                            new AscBy(
                                new Property(
                                    'id'
                                )
                            )
                        )
                    ),
                    1,
                    0
                )
            )->next()
                ->asKeyValue()['offset']
        );
    }

    public function testGettingTopWillEqualsLimit(): void
    {
        $this->assertEquals(
            1,
            PagedQuery::forGettingTop(
                new Query(
                    new Sorts(
                        new AscBy(
                            new Property('id')
                        )
                    )
                ),
                1
            )->asKeyValue()['limit']
        );
    }
}
