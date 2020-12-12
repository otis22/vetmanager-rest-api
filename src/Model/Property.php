<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\Model;

use ElegantBro\Interfaces\Stringify;

final class Property implements Stringify
{
    /**
     * @var string
     */
    private $property;

    /**
     * Property constructor.
     * @param string $property
     */
    public function __construct(string $property)
    {
        $this->property = $property;
    }

    /**
     * @inheritDoc
     */
    public function asString(): string
    {
        return $this->property;
    }
}
