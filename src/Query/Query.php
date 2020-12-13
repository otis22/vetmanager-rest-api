<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\Query;

use Otis22\PhpInterfaces\KeyValue;

final class Query implements KeyValue
{
    /**
     * @var array<KeyValue>
     */
    private $queries;

    /**
     * Query constructor.
     * @param KeyValue ...$queries
     */
    public function __construct(KeyValue ...$queries)
    {
        $this->queries = $queries;
    }

    /**
     * @inheritDoc
     */
    public function asKeyValue(): array
    {
        return array_reduce(
            $this->queries,
            function (array $carry, KeyValue $current) {
                return array_merge($carry, $current->asKeyValue());
            },
            []
        );
    }
}
