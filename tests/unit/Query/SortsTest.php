<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\Query;

use Otis22\VetmanagerRestApi\Model\Property;
use Otis22\VetmanagerRestApi\Query\Sort\AscBy;
use PHPUnit\Framework\TestCase;

class SortsTest extends TestCase
{

    public function testAsArray(): void
    {
        $this->assertEquals(
            [
                'sort' => '[{"property":"test","direction":"ASC"}]'
            ],
            (
                new Sorts(
                    new AscBy(
                        new Property('test')
                    )
                )
            )->asKeyValue()
        );
    }
}
