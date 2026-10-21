<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi;

use Otis22\VetmanagerRestApi\Headers\Auth\ApiKey;
use Otis22\VetmanagerRestApi\Headers\Auth\ByApiKey;
use Otis22\VetmanagerRestApi\Headers\WithAuthAndParams;
use Otis22\VetmanagerRestApi\Query\Builder;
use PHPUnit\Framework\TestCase;

class ContractEdgeCasesTest extends TestCase
{
    /**
     * @dataProvider invalidWhereArguments
     * @param array<mixed> $arguments
     */
    public function testWhereRejectsInvalidArity(array $arguments): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new Builder())->where(...$arguments);
    }

    /**
     * @return array<string, array{array<mixed>}>
     */
    public function invalidWhereArguments(): array
    {
        return [
            'none' => [[]],
            'one' => [['id']],
            'four' => [['id', '=', 1, 'extra']]
        ];
    }

    public function testWhereRejectsNullValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new Builder())->where('id', null);
    }

    public function testFiltersAndSortsKeepOrderAndSerialization(): void
    {
        $query = (new Builder())
            ->where('client_id', 'in', [1, 2])
            ->where('active', '0')
            ->orderBy('id', 'DESC')
            ->orderBy('client_id', 'asc');

        $this->assertSame(
            [
                'filter' => '[{"property":"client_id","value":[1,2],"operator":"in"},' .
                    '{"property":"active","value":"0"}]',
                'sort' => '[{"property":"id","direction":"DESC"},' .
                    '{"property":"client_id","direction":"ASC"}]'
            ],
            $query->asKeyValue()
        );
    }

    public function testNextPagePreservesQueryAndOriginalPage(): void
    {
        $first = (new Builder())->where('client_id', '1')->paginate(25, 0);
        $second = $first->next();

        $this->assertSame(0, $first->asKeyValue()['offset']);
        $this->assertSame(25, $second->asKeyValue()['offset']);
        $this->assertSame($first->asKeyValue()['filter'], $second->asKeyValue()['filter']);
        $this->assertSame($first->asKeyValue()['sort'], $second->asKeyValue()['sort']);
    }

    public function testModelAndUriBoundaryValues(): void
    {
        $this->assertSame('/rest/api/invoice', uri('invoice')->asString());
        $this->assertSame('/rest/api/invoice/0', uri('invoice', 0)->asString());
    }

    public function testModelValidationIsCaseSensitive(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        uri('Invoice')->asString();
    }

    public function testPublicHelpersAndCustomHeadersKeepExactKeys(): void
    {
        $this->assertSame(['X-REST-API-KEY' => 'key'], byApiKey('key')->asKeyValue());
        $this->assertSame(
            ['X-APP-NAME' => 'app', 'X-USER-TOKEN' => 'token'],
            byToken('app', 'token')->asKeyValue()
        );
        $this->assertSame(
            ['X-SERVICE-REST-API-KEY' => 'key', 'X-SERVICE-NAME' => 'service'],
            byServiceApiKey('service', 'key')->asKeyValue()
        );
        $this->assertSame(
            ['X-REST-API-KEY' => 'key', 'X-REST-TIME-ZONE' => '+02:00'],
            (new WithAuthAndParams(
                new ByApiKey(new ApiKey('key')),
                ['X-REST-TIME-ZONE' => '+02:00']
            ))->asKeyValue()
        );
    }
}
