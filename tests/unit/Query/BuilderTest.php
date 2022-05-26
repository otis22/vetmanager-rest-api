<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\Query;

use PHPUnit\Framework\TestCase;

class BuilderTest extends TestCase
{
    public function testTopOne(): void
    {
        $top = (new Builder())->top(1);
        $this->assertEquals(
            [
                'sort' => '[{"property":"id","direction":"ASC"}]',
                'limit' => 1,
                'offset' => 0
            ],
            $top->asKeyValue()
        );
    }

    public function testPaginateAll(): void
    {
        $paginateAll = (new Builder())->paginateAll();
        $this->assertEquals(
            [
                'sort' => '[{"property":"id","direction":"ASC"}]',
                'limit' => 100,
                'offset' => 0
            ],
            $paginateAll->asKeyValue()
        );
    }

    public function testPaginateLimit30FromPage5(): void
    {
        $paginate = (new Builder())->paginate(30, 5);
        $this->assertEquals(
            [
                'sort' => '[{"property":"id","direction":"ASC"}]',
                'limit' => 30,
                'offset' => 150
            ],
            $paginate->asKeyValue()
        );
    }

    public function testWhereClientIdEqualTo2(): void
    {
        $top = (new Builder())
            ->where('client_id', '2')
            ->top(1);
        $this->assertEquals(
            [
                'sort' => '[{"property":"id","direction":"ASC"}]',
                'filter' => '[{"property":"client_id","value":"2"}]',
                'limit' => 1,
                'offset' => 0
            ],
            $top->asKeyValue()
        );
    }

    public function testWhereClientIdInList1Or2(): void
    {
        $top = (new Builder())
            ->where('client_id', 'in', [1, 2])
            ->top(1);
        $this->assertEquals(
            [
                'sort' => '[{"property":"id","direction":"ASC"}]',
                'filter' => '[{"property":"client_id","value":[1,2],"operator":"in"}]',
                'limit' => 1,
                'offset' => 0
            ],
            $top->asKeyValue()
        );
    }

    public function testOrderByAddDesc(): void
    {
        $top = (new Builder())
            ->orderBy('client_id', 'desc')
            ->top(1);
        $this->assertEquals(
            [
                'sort' => '[{"property":"client_id","direction":"DESC"}]',
                'limit' => 1,
                'offset' => 0
            ],
            $top->asKeyValue()
        );
    }

    public function testOrderByInvalidOrder(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new Builder())->orderBy('id', 'Invalid Desc');
    }

    public function testWhereInvalidOperator(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new Builder())->where('id', 'Invalid Operator', 'test');
    }
}
