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

use function Otis22\VetmanagerRestApi\byApiKey;
use function Otis22\VetmanagerRestApi\uri;
use function Otis22\VetmanagerUrl\url;

final class AllRecordsTest extends TestCase
{
    public function testAllRecords(): void
    {
        $modelWithMultiplePages = 'comboManualItem';
        $client = new Client(
            [
                'base_uri' => url(
                    strval(
                        getenv('TEST_DOMAIN_NAME')
                    )
                )->asString()
            ]
        );
        $paged =  PagedQuery::forGettingAll(
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
}
