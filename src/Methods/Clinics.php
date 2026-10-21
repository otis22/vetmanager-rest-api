<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\Methods;

use GuzzleHttp\ClientInterface;
use Otis22\VetmanagerRestApi\Headers;
use Psr\Http\Message\ResponseInterface;

use function Otis22\VetmanagerRestApi\uri;

/**
 * Optional method layer for the read-only clinics collection.
 */
final class Clinics
{
    /** @var ClientInterface */
    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param array<string, mixed> $query
     */
    public function get(Headers $headers, array $query = ['limit' => 1]): ResponseInterface
    {
        return $this->client->request(
            'GET',
            uri('clinics')->asString(),
            ['headers' => $headers->asKeyValue(), 'query' => $query]
        );
    }
}
