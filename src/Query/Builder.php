<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\Query;

use Otis22\VetmanagerRestApi\Query\Filter\EqualTo;
use Otis22\VetmanagerRestApi\Query\Filter\InArray;
use Otis22\VetmanagerRestApi\Query\Filter\LessOrEqualThan;
use Otis22\VetmanagerRestApi\Query\Filter\LessThan;
use Otis22\VetmanagerRestApi\Query\Filter\Like;
use Otis22\VetmanagerRestApi\Query\Filter\MoreOrEqualThan;
use Otis22\VetmanagerRestApi\Query\Filter\MoreThan;
use Otis22\VetmanagerRestApi\Query\Filter\NotEqualTo;
use Otis22\VetmanagerRestApi\Query\Filter\NotInArray;
use Otis22\VetmanagerRestApi\Model\Property;
use Otis22\VetmanagerRestApi\Query\Filter\Value\ArrayValue;
use Otis22\VetmanagerRestApi\Query\Filter\Value\StringValue;
use Otis22\VetmanagerRestApi\Query\Sort\AscBy;
use Otis22\VetmanagerRestApi\Query\Sort\DescBy;

/**
 * @method self where(string $property, ...$args) $args can be $value, or $operator + $value
 */
final class Builder
{
    /**
     * @var array<int, SortBy>
     */
    private $sorts = [];
    /**
     * @var array<int, Filter>
     */
    private $filters = [];

    /**
     * @param string $methodName
     * @param array<mixed> $args
     * @return $this|void
     */
    public function __call(string $methodName, array $args)
    {
        if ($methodName === 'where') {
            if (count($args) === 2) {
                return $this->filterWhere($args[0], '=', $args[1]);
            }
            if (count($args) === 3) {
                return $this->filterWhere($args[0], $args[1], $args[2]);
            }
            throw new \InvalidArgumentException('Wrong params');
        }
    }

    /**
     * @param string $column
     * @param string $direction
     * @return $this
     */
    public function orderBy(string $column, string $direction): self
    {
        $direction = strtolower($direction);
        if ($direction === 'asc') {
            $this->sorts[] = new AscBy(new Property($column));
        } elseif ($direction === 'desc') {
            $this->sorts[] = new DescBy(new Property($column));
        } else {
            throw new \InvalidArgumentException('Invalid direction: ' . $direction);
        }

        return clone $this;
    }

    /**
     * @param string $name
     * @param string $operator
     * @param mixed $value
     * @return $this
     */
    protected function filterWhere(string $name, string $operator, $value): self
    {
        if (is_null($value)) {
            throw new \InvalidArgumentException('Vetmanager api does not work with null values');
        }

        if ($operator === '=' || $operator === EqualTo::class) {
            $this->filters[] = new EqualTo(new Property($name), new StringValue($value));
        } elseif ($operator === 'in' || $operator === InArray::class) {
            $this->filters[] = new InArray(new Property($name), new ArrayValue($value));
        } elseif ($operator === '<=' || $operator === LessOrEqualThan::class) {
            $this->filters[] = new LessOrEqualThan(new Property($name), new StringValue($value));
        } elseif ($operator === '<' || $operator === LessThan::class) {
            $this->filters[] = new LessThan(new Property($name), new StringValue($value));
        } elseif ($operator === 'like' || $operator === Like::class) {
            $this->filters[] = new Like(new Property($name), new StringValue($value));
        } elseif ($operator === '>=' || $operator === MoreOrEqualThan::class) {
            $this->filters[] = new MoreOrEqualThan(new Property($name), new StringValue($value));
        } elseif ($operator === '>' || $operator === MoreThan::class) {
            $this->filters[] = new MoreThan(new Property($name), new StringValue($value));
        } elseif ($operator === '!=' || $operator === '<>' || $operator === NotEqualTo::class) {
            $this->filters[] = new NotEqualTo(new Property($name), new StringValue($value));
        } elseif ($operator === 'not in' || $operator === NotInArray::class) {
            $this->filters[] = new NotInArray(new Property($name), new ArrayValue($value));
        } else {
            throw new \InvalidArgumentException('Invalid operator: ' . $operator);
        }

        return clone $this;
    }

    /**
     * @return Query
     */
    private function query(): Query
    {
        $params = [];

        if (!empty($this->filters)) {
            $params[] = new Filters(...$this->filters);
        }

        if (!empty($this->sorts)) {
            $params[] = new Sorts(...$this->sorts);
        } else {
            $params[] = new Sorts(new AscBy(new Property('id')));
        }
        return new Query(... $params);
    }

    public function paginateAll(): PagedQuery
    {
        return PagedQuery::forGettingAll(
            $this->query()
        );
    }

    public function paginate(int $limit, int $page): PagedQuery
    {
        return new PagedQuery(
            $this->query(),
            $limit,
            $page
        );
    }

    public function top(int $limit): PagedQuery
    {
        return PagedQuery::forGettingTop(
            $this->query(),
            $limit
        );
    }
}
