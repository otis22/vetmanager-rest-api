<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\Query\Filter\Value;

use Otis22\VetmanagerRestApi\Query\Filter\Value;

final class StringValue implements Value
{
    /**
     * @var string
     */
    private $value;

    /**
     * StringValue constructor.
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * @inheritDoc
     */
    public function asString(): string
    {
        return $this->value;
    }
}
