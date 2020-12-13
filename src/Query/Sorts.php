<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\Query;

use ElegantBro\Arrayee\Just;
use ElegantBro\Interfaces\Arrayee;
use Otis22\PhpInterfaces\KeyValue;
use Otis22\VetmanagerRestApi\Query\Json\EncodedMappedKeyValueArray;

final class Sorts implements KeyValue
{
    /**
     * @var Arrayee
     */
    private $sortsBy;

    /**
     * Sorts constructor.
     * @param SortBy ...$sortsBy
     */
    public function __construct(SortBy ...$sortsBy)
    {
        $this->sortsBy = new Just($sortsBy);
    }

    /**
     * @inheritDoc
     */
    public function asKeyValue(): array
    {
        return [
            'sort' => (
                new EncodedMappedKeyValueArray($this->sortsBy)
            )->asString()
        ];
    }
}
