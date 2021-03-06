<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\Query;

use ElegantBro\Arrayee\Just;
use ElegantBro\Interfaces\Arrayee;
use Otis22\PhpInterfaces\KeyValue;
use Otis22\VetmanagerRestApi\Query\Json\EncodedMappedKeyValueArray;

final class Filters implements KeyValue
{
    /**
     * @var Arrayee
     */
    private $filters;

    /**
     * Filters constructor.
     * @param Filter ... $filters
     */
    public function __construct(Filter ...$filters)
    {
        $this->filters = new Just($filters);
    }

    /**
     * @inheritDoc
     */
    public function asKeyValue(): array
    {
        return [
            'filter' => (
                new EncodedMappedKeyValueArray($this->filters)
            )->asString()
        ];
    }
}
