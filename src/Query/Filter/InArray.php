<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\Query\Filter;

use Otis22\VetmanagerRestApi\Query\Filter;
use Otis22\VetmanagerRestApi\Model\Property;
use Otis22\VetmanagerRestApi\Query\Filter\Value\ArrayValue;

final class InArray implements Filter
{
    /**
     * @var string
     */
    private $operator = 'in';
    /**
     * @var Property
     */
    private $property;
    /**
     * @var ArrayValue
     */
    private $value;

    /**
     * InArray constructor.
     * @param Property $property
     * @param ArrayValue $value
     */
    public function __construct(Property $property, ArrayValue $value)
    {
        $this->property = $property;
        $this->value = $value;
    }

    /**
     * @inheritDoc
     */
    public function asKeyValue(): array
    {
        $filterSettings['property'] = $this->property->asString();
        $filterSettings['value'] = $this->value->asString();
        $filterSettings['operator'] = $this->operator;
        return $filterSettings;
    }
}
