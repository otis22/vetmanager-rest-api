<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\integration;

use GuzzleHttp\Client;
use Otis22\VetmanagerRestApi\Headers;
use PHPUnit\Framework\TestCase;
use Otis22\VetmanagerRestApi\Support\ClientFactory;

use function Otis22\VetmanagerRestApi\byApiKey;
use function Otis22\VetmanagerRestApi\byServiceApiKey;
use function Otis22\VetmanagerRestApi\uri;
use function getenv;

final class AuthTest extends TestCase
{
    public function testApiKey(): void
    {
        $this->assertTrue(
            $this->jsonTestResponse(
                byApiKey(
                    (string) getenv("TEST_API_KEY")
                )
            )->success
        );
    }

    public function testServiceApiKey(): void
    {
        $this->assertTrue(
            $this->jsonTestResponse(
                byServiceApiKey(
                    (string) getenv('SERVICE_NAME'),
                    (string) getenv('SERVICE_API_KEY')
                )
            )->success
        );
    }

    public function jsonTestResponse(Headers $auth): \stdClass
    {
        $client = ClientFactory::createClient();
        $request = $client->request(
            'GET',
            uri('clinics')->asString(),
            [
                'headers' => $auth->asKeyValue()
            ]
        );
        return json_decode(
            (string) $request->getBody(),
            false,
            512,
            JSON_THROW_ON_ERROR
        );
    }
}
