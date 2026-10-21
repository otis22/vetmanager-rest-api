<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\integration;

use Otis22\VetmanagerRestApi\Methods\Clinics;
use Otis22\VetmanagerRestApi\Support\ClientFactory;
use PHPUnit\Framework\TestCase;

use function Otis22\VetmanagerRestApi\byApiKey;
use function Otis22\VetmanagerRestApi\namedPath;
use function Otis22\VetmanagerRestApi\uri;

final class OpenApiReadOnlyTest extends TestCase
{
    /**
     * @dataProvider readOnlyPaths
     */
    public function testConfirmedGet(string $path): void
    {
        $client = ClientFactory::createClient();
        $response = $client->request('GET', $path, [
            'headers' => byApiKey((string)getenv('TEST_API_KEY'))->asKeyValue(),
            'query' => ['limit' => 1],
            'connect_timeout' => 5,
            'timeout' => 15,
        ]);
        self::assertSame(200, $response->getStatusCode());
        self::assertStringContainsString('application/json', $response->getHeaderLine('Content-Type'));
        $json = json_decode((string)$response->getBody(), true);
        self::assertIsArray($json);
        self::assertSame(true, $json['success'] ?? null);
    }

    /** @return array<string, array{string}> */
    public function readOnlyPaths(): array
    {
        return [
            'ComboManualName' => [uri('ComboManualName')->asString()],
            'MedicalCards' => [uri('MedicalCards')->asString()],
            'AllDiagnoses' => [namedPath('MedicalCards/AllDiagnoses')->asString()],
            'MedcardsTextTemplates' => [namedPath('MedicalCards/MedcardsTextTemplates')->asString()],
            'anonymousList' => [namedPath('user/anonymousList')->asString()],
        ];
    }

    public function testClinicsMethod(): void
    {
        $client = ClientFactory::createClient();
        $response = (new Clinics($client))->get(byApiKey((string)getenv('TEST_API_KEY')));
        self::assertSame(200, $response->getStatusCode());
        self::assertStringContainsString('application/json', $response->getHeaderLine('Content-Type'));
        $json = json_decode((string)$response->getBody(), true);
        self::assertIsArray($json);
        self::assertSame(true, $json['success'] ?? null);
    }
}
