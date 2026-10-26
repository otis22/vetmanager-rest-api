<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\Methods;

use GuzzleHttp\ClientInterface;
use Otis22\VetmanagerRestApi\Headers;
use Psr\Http\Message\ResponseInterface;

use function Otis22\VetmanagerRestApi\uri;

/**
 * Optional JSON POST and PUT for Unit; the caller owns the payload and record lifecycle.
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
}
