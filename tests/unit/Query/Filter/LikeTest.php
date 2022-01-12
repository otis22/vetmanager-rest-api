<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\Query\Filter;

use Otis22\VetmanagerRestApi\Model\Property;
use Otis22\VetmanagerRestApi\Query\Filter\Value\StringValue;
use PHPUnit\Framework\TestCase;

class LikeTest extends TestCase
{
    public function testAsKeyValue(): void
    {
        $this->assertEquals(
            [
                'operator' => 'like',
                'property' => 'test',
                'value' => 'test'
            ],
            (
                new Like(
                    new Property('test'),
                    new StringValue('test')
                )
            )->asKeyValue()
        );
    }
}
