<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\Methods;

use GuzzleHttp\ClientInterface;
use Otis22\VetmanagerRestApi\Headers;
use Psr\Http\Message\ResponseInterface;

use function Otis22\VetmanagerRestApi\uri;

/**
 * Optional POST, PUT and DELETE for Unit; the caller owns the record lifecycle.
 */
final class Unit
{
    /** @var ClientInterface */
    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param array<string, mixed> $body
     */
    public function post(Headers $headers, array $body): ResponseInterface
    {
        return $this->client->request(
            'POST',
            uri('Unit')->asString(),
            ['headers' => $headers->asKeyValue(), 'json' => $body]
        );
    }

    /**
     * @param array<string, mixed> $body
     */
    public function put(Headers $headers, int $id, array $body): ResponseInterface
    {
        return $this->client->request(
            'PUT',
            uri('Unit', $id)->asString(),
            ['headers' => $headers->asKeyValue(), 'json' => $body]
        );
    }

    public function delete(Headers $headers, int $id): ResponseInterface
    {
        return $this->client->request(
            'DELETE',
            uri('Unit', $id)->asString(),
            ['headers' => $headers->asKeyValue()]
        );
    }
}
