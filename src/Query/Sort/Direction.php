<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\Query\Sort;

use ElegantBro\Interfaces\Stringify;

final class Direction implements Stringify
{
    /**
     * @var string
     */
    private $direction;

    /**
     * Direction constructor.
     * @param string $direction
     */
    public function __construct(string $direction)
    {
        $this->direction = $direction;
    }

    /**
     * @inheritDoc
     */
    public function asString(): string
    {
        if (
            in_array(
                strtolower($this->direction),
                ['asc', 'desc']
            )
        ) {
            return strtoupper($this->direction);
        }
        throw new \UnexpectedValueException("Direction " . $this->direction . " is not excepted");
    }
}
