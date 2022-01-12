<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\Query\Sort;

use Otis22\VetmanagerRestApi\Model\Property;
use PHPUnit\Framework\TestCase;

class DescByTest extends TestCase
{
    public function testAsKeyValue(): void
    {
        $this->assertEquals(
            [
                'direction' => 'DESC',
                'property' => 'test'
            ],
            (
            new DescBy(
                new Property('test')
            )
            )->asKeyValue()
        );
    }
}
