<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\Query;

use Otis22\PhpInterfaces\KeyValue;

final class PagedQuery implements KeyValue
{
    /**
     * @var Query
     */
    private $query;
    /**
     * @var integer
     */
    private $limit;
    /**
     * @var integer
     */
    private $currentPage;

    /**
     * Paged constructor.
     * @param Query $query
     * @param int $limit
     * @param int $currentPage
     */
    public function __construct(Query $query, int $limit, int $currentPage)
    {
        $this->query = $query;
        $this->limit = $limit;
        $this->currentPage = $currentPage;
    }

    /**
     * @param Query $query
     * @return PagedQuery
     */
    public static function forGettingAll(Query $query): PagedQuery
    {
        return self::withMaxPageSize($query, 0);
    }

    /**
     * @param Query $query
     * @param int $currentPage
     * @return PagedQuery
     */
    public static function withMaxPageSize(Query $query, int $currentPage): PagedQuery
    {
        return new PagedQuery($query, 100, $currentPage);
    }

    /**
     * @return PagedQuery
     */
    public function next(): PagedQuery
    {
        return new PagedQuery(
            $this->query,
            $this->limit,
            $this->currentPage + 1
        );
    }

    /**
     * @inheritDoc
     */
    public function asKeyValue(): array
    {
        $query = $this->queryWithPageData();
        if (!isset($query['sort'])) {
            throw new \Exception("Query must contains sort key for correct pagination work");
        }
        return $query;
    }

    /**
     * @return array<string, string>
     */
    private function queryWithPageData(): array
    {
        return array_merge(
            $this->query->asKeyValue(),
            [
                'limit' => $this->limit,
                'offset' => $this->limit * $this->currentPage
            ]
        );
    }
}
