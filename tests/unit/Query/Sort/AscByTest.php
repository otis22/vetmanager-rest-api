<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\Query\Sort;

use Otis22\VetmanagerRestApi\Model\Property;
use PHPUnit\Framework\TestCase;

class AscByTest extends TestCase
{

    public function testAsKeyValue(): void
    {
        $this->assertEquals(
            [
                'direction' => 'ASC',
                'property' => 'test'
            ],
            (
                new AscBy(
                    new Property('test')
                )
            )->asKeyValue()
        );
    }
}
