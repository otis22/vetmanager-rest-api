<?php

namespace Tests\integration;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Otis22\VetmanagerRestApi\Query\Query;
use Otis22\VetmanagerRestApi\Query\Filters;
use Otis22\VetmanagerRestApi\Query\Filter\NotInArray;
use Otis22\VetmanagerRestApi\Model\Property;
use Otis22\VetmanagerRestApi\Query\Filter\Value\ArrayValue;
use Otis22\VetmanagerRestApi\Query\Sorts;
use Otis22\VetmanagerRestApi\Query\Sort\AscBy;
use Otis22\VetmanagerRestApi\Query\PagedQuery;

use function Otis22\VetmanagerUrl\url;
use function Otis22\VetmanagerRestApi\uri;
use function Otis22\VetmanagerRestApi\byApiKey;
use function getenv;

class QueriesTest extends TestCase
{
    public function testInvoiceWithFewFilters(): void
    {
        $client = new Client(
            [
                'base_uri' => url(
                    strval(
                        getenv('TEST_DOMAIN_NAME')
                    )
                )->asString()
            ]
        );
        $request = $client->request(
            'GET',
            uri('invoice')->asString(),
            [
                'headers' => byApiKey(
                    strval(
                        getenv("TEST_API_KEY")
                    )
                )->asKeyValue(),
                'query' => PagedQuery::forGettingTop(
                    new Query(
                        new Filters(
                            new NotInArray(
                                new Property('status'),
                                new ArrayValue(['exec','save','closed','archived'])
                            )
                        ),
                        new Sorts(
                            new AscBy(
                                new Property('id')
                            )
                        )
                    ),
                    1
                )->asKeyValue()
            ]
        );
        $json = json_decode(
            strval(
                $request->getBody()
            )
        );
        $this->assertEquals($json->data->invoice[0]->status, 'deleted');
    }
}
