<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\Query;

use ElegantBro\Arrayee\Just;
use ElegantBro\Arrayee\Mapped;
use ElegantBro\Arrayee\Merged;
use ElegantBro\Interfaces\Arrayee;
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
        $result = [];
        foreach ($this->queries as $query) {
            $result = (
                new Merged(
                    new Just($result),
                    new Just($query->asKeyValue())
                )
            )->asArray();
        }
        return $result;
    }
}
