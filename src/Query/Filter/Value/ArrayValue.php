<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\Query\Filter\Value;

use _HumbugBoxbde535255540\Nette\Neon\Exception;
use ElegantBro\Stringify\Json\JsonEncoded;
use Otis22\VetmanagerRestApi\Query\Filter\Value;

final class ArrayValue implements Value
{
    /**
     * @var int[] | string[]
     */
    private $value;

    /**
     * ArrayValue constructor.
     * @param int[]|string[] $value
     */
    public function __construct(array $value)
    {
        $this->value = $value;
    }

    /**
     * @inheritDoc
     */
    public function asString(): string
    {
        try {
            return JsonEncoded::default($this->value)
                ->asString();
        } catch (\Throwable $exception) {
            throw new \UnexpectedValueException(
                "Invalid value "
                . print_r($this->value, true)
                . ". "
                .  $exception->getMessage()
            );
        }
    }
}
