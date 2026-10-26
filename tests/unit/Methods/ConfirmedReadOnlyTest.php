<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\unit\Methods;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Otis22\VetmanagerRestApi\Methods\MedicalCardsAllDiagnoses;
use Otis22\VetmanagerRestApi\Methods\MedicalCardsMedcardsTextTemplates;
use Otis22\VetmanagerRestApi\Methods\UserAnonymousList;
use PHPUnit\Framework\TestCase;

use function Otis22\VetmanagerRestApi\byApiKey;

final class ConfirmedReadOnlyTest extends TestCase
{
    /**
     * @return array<string, array{string, string}>
     */
    public function routes(): array
    {
        return [
            'all diagnoses' => [MedicalCardsAllDiagnoses::class, '/rest/api/MedicalCards/AllDiagnoses'],
            'text templates' => [
                MedicalCardsMedcardsTextTemplates::class,
                '/rest/api/MedicalCards/MedcardsTextTemplates',
            ],
            'anonymous list' => [UserAnonymousList::class, '/rest/api/user/anonymousList'],
        ];
    }

    /**
     * @dataProvider routes
     * @param class-string $class
     */
    public function testDefaultRequestForEachRoute(string $class, string $path): void
    {
        $response = new Response(200, [], '{"success":true}');
        $client = $this->createMock(ClientInterface::class);
        $client->expects(self::once())->method('request')->with(
            'GET',
            $path,
            ['headers' => byApiKey('test-key')->asKeyValue(), 'query' => ['limit' => 1]]
        )->willReturn($response);

        /** @var MedicalCardsAllDiagnoses|MedicalCardsMedcardsTextTemplates|UserAnonymousList $method */
        $method = new $class($client);
        self::assertSame($response, $method->get(byApiKey('test-key')));
    }

    /**
     * @dataProvider routes
     * @param class-string $class
     */
    public function testCustomQueryIsPassedUnchanged(string $class, string $path): void
    {
        $query = ['limit' => 'not-an-integer', 'filter' => ['nested' => true]];
        $response = new Response(200);
        $client = $this->createMock(ClientInterface::class);
        $client->expects(self::once())->method('request')->with(
            'GET',
            $path,
            ['headers' => byApiKey('test-key')->asKeyValue(), 'query' => $query]
        )->willReturn($response);

        /** @var MedicalCardsAllDiagnoses|MedicalCardsMedcardsTextTemplates|UserAnonymousList $method */
        $method = new $class($client);
        self::assertSame($response, $method->get(byApiKey('test-key'), $query));
    }

    /**
     * @dataProvider routes
     * @param class-string $class
     */
    public function testClientExceptionPassesThroughUnchanged(string $class, string $path): void
    {
        $exception = new \RuntimeException('client failure');
        $client = $this->createMock(ClientInterface::class);
        $client->expects(self::once())->method('request')->with(
            'GET',
            $path,
            ['headers' => byApiKey('test-key')->asKeyValue(), 'query' => ['limit' => 1]]
        )->willThrowException($exception);

        /** @var MedicalCardsAllDiagnoses|MedicalCardsMedcardsTextTemplates|UserAnonymousList $method */
        $method = new $class($client);
        try {
            $method->get(byApiKey('test-key'));
            self::fail('Expected client exception');
        } catch (\RuntimeException $actual) {
            self::assertSame($exception, $actual);
        }
    }

    public function testGuzzleRequestHasNoBodyAndPreservesHttpErrorsConfiguration(): void
    {
        $history = [];
        $stack = HandlerStack::create(new MockHandler([new Response(401)]));
        $stack->push(Middleware::history($history));
        $throwing = new Client(['base_uri' => 'https://example.test/', 'handler' => $stack]);

        try {
            (new MedicalCardsAllDiagnoses($throwing))->get(byApiKey('test-key'));
            self::fail('Expected Guzzle ClientException');
        } catch (ClientException $exception) {
            self::assertSame(401, $exception->getResponse()->getStatusCode());
        }
        self::assertSame(
            'https://example.test/rest/api/MedicalCards/AllDiagnoses?limit=1',
            (string)$history[0]['request']->getUri()
        );
        self::assertSame('test-key', $history[0]['request']->getHeaderLine('X-REST-API-KEY'));
        self::assertSame('', (string)$history[0]['request']->getBody());

        $returning = new Client([
            'handler' => HandlerStack::create(new MockHandler([new Response(404)])),
            'http_errors' => false,
        ]);
        self::assertSame(404, (new UserAnonymousList($returning))->get(byApiKey('test-key'))->getStatusCode());
    }
}
