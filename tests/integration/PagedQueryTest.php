<?php

declare(strict_types=1);

namespace Tests\integration;

use GuzzleHttp\Client;
use Otis22\VetmanagerRestApi\Model\Property;
use Otis22\VetmanagerRestApi\Query\PagedQuery;
use Otis22\VetmanagerRestApi\Query\Sort\AscBy;
use Otis22\VetmanagerRestApi\Query\Sorts;
use Otis22\VetmanagerRestApi\Query\Query;
use PHPUnit\Framework\TestCase;
use Otis22\VetmanagerRestApi\Support\ClientFactory;

use function Otis22\VetmanagerRestApi\byApiKey;
use function Otis22\VetmanagerRestApi\uri;
use function Otis22\VetmanagerUrl\url;
use function getenv;
use function strval;

final class PagedQueryTest extends TestCase
{
    public function testAllRecords(): void
    {
        $modelWithMultiplePages = 'comboManualItem';
        $client = ClientFactory::createClient();
        $paged = PagedQuery::forGettingAll(
            new Query(
                new Sorts(
                    new AscBy(
                        new Property('id')
                    )
                )
            )
        );
        $headers = byApiKey(
            strval(
                getenv("TEST_API_KEY")
            )
        )->asKeyValue();
        $result = [];
        do {
            $response = json_decode(
                strval(
                    $client->request(
                        'GET',
                        uri($modelWithMultiplePages)->asString(),
                        [
                            'headers' => $headers,
                            'query' => $paged->asKeyValue()
                        ]
                    )->getBody()
                ),
                true
            );
            $paged = $paged->next();
            $result = array_merge(
                $response['data'][$modelWithMultiplePages],
                $result
            );
        } while (count($result) < $response['data']['totalCount']);
        $this->assertTrue(count($result) == $response['data']['totalCount']);
    }

    public function testTop1Clients(): void
    {
        $client = ClientFactory::createClient();
        $top1 = PagedQuery::forGettingTop(
            new Query(
                new Sorts(
                    new AscBy(
                        new Property('id')
                    )
                )
            ),
            1
        );
        $response = json_decode(
            strval(
                $client->request(
                    'GET',
                    uri('client')->asString(),
                    [
                        'headers' => byApiKey(
                            strval(
                                getenv("TEST_API_KEY")
                            )
                        )->asKeyValue(),
                        'query' => $top1->asKeyValue()
                    ]
                )->getBody()
            ),
            true
        );
        $this->assertEquals(count($response['data']['client']), 1);
    }
}
