<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\unit\Methods;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Otis22\VetmanagerRestApi\Methods\Clinics;
use PHPUnit\Framework\TestCase;

use function Otis22\VetmanagerRestApi\byApiKey;

final class ClinicsTest extends TestCase
{
    public function testGetPassesExactRequestAndReturnsResponse(): void
    {
        $response = new Response(200, [], '{"success":true}');
        $history = [];
        $stack = HandlerStack::create(new MockHandler([$response]));
        $stack->push(Middleware::history($history));
        $client = new Client(['base_uri' => 'https://example.test/', 'handler' => $stack]);

        $actual = (new Clinics($client))->get(byApiKey('test-key'));

        self::assertSame($response, $actual);
        self::assertCount(1, $history);
        $request = $history[0]['request'];
        self::assertSame('GET', $request->getMethod());
        self::assertSame('https://example.test/rest/api/clinics?limit=1', (string)$request->getUri());
        self::assertSame('test-key', $request->getHeaderLine('X-REST-API-KEY'));
        self::assertSame('', (string)$request->getBody());
    }

    public function testCustomQueryIsForwardedWithoutExtraLimit(): void
    {
        $history = [];
        $stack = HandlerStack::create(new MockHandler([new Response(200)]));
        $stack->push(Middleware::history($history));
        $client = new Client(['base_uri' => 'https://example.test/', 'handler' => $stack]);

        (new Clinics($client))->get(byApiKey('test-key'), ['limit' => 'not-an-integer']);

        self::assertSame('limit=not-an-integer', $history[0]['request']->getUri()->getQuery());
    }

    public function testHttpErrorsConfigurationIsPreserved(): void
    {
        $throwing = new Client(['handler' => HandlerStack::create(new MockHandler([new Response(401)]))]);
        try {
            (new Clinics($throwing))->get(byApiKey('test-key'));
            self::fail('Expected the Guzzle exception');
        } catch (ClientException $exception) {
            self::assertSame(401, $exception->getResponse()->getStatusCode());
        }

        $returning = new Client([
            'handler' => HandlerStack::create(new MockHandler([new Response(404)])),
            'http_errors' => false,
        ]);
        self::assertSame(404, (new Clinics($returning))->get(byApiKey('test-key'))->getStatusCode());
    }
}
