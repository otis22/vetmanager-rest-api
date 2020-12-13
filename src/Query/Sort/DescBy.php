<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\Query\Sort;

use Otis22\VetmanagerRestApi\Model\Property;
use Otis22\VetmanagerRestApi\Query\SortBy;

final class DescBy implements SortBy
{
    /**
     * @var Property
     */
    private $property;
    /**
     * @var Direction
     */
    private $direction;

    /**
     * DescBy constructor.
     * @param Property $property
     */
    public function __construct(Property $property)
    {
        $this->property = $property;
        $this->direction = new Direction('DESC');
    }

    /**
     * @inheritDoc
     */
    public function asKeyValue(): array
    {
        return [
            'property' => $this->property->asString(),
            'direction' => $this->direction->asString()
        ];
    }
}
