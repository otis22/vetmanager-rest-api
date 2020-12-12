<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\Query;

use ElegantBro\Arrayee\Just;
use ElegantBro\Arrayee\Mapped;
use ElegantBro\Interfaces\Arrayee;
use ElegantBro\Stringify\Json\JsonEncoded;

final class Filters implements Arrayee
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
     * @return array <string, string>
     * @throws \Exception
     */
    public function asArray(): array
    {
        return [
            'filter' => JsonEncoded::default(
                (
                    new Mapped(
                        $this->filters,
                        function (Filter $filter) {
                            return $filter->asKeyValue();
                        }
                    )
                )->asArray()
            )->asString()
        ];
    }
}
