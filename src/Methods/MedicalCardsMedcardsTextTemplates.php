<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\Methods;

use GuzzleHttp\ClientInterface;
use Otis22\VetmanagerRestApi\Headers;
use Psr\Http\Message\ResponseInterface;

use function Otis22\VetmanagerRestApi\namedPath;

/**
 * Optional read-only method for the MedicalCards/MedcardsTextTemplates route.
 */
final class MedicalCardsMedcardsTextTemplates
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
            namedPath('MedicalCards/MedcardsTextTemplates')->asString(),
            ['headers' => $headers->asKeyValue(), 'query' => $query]
        );
    }
}
