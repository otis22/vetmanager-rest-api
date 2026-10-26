<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\unit\Methods;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Otis22\VetmanagerRestApi\Methods\Unit;
use PHPUnit\Framework\TestCase;

use function Otis22\VetmanagerRestApi\byApiKey;

final class UnitTest extends TestCase
{
    public function testPostSendsJsonAndReturnsOriginalResponse(): void
    {
        $response = new Response(201, [], '{"success":true}');
        $history = [];
        $stack = HandlerStack::create(new MockHandler([$response]));
        $stack->push(Middleware::history($history));
        $client = new Client(['base_uri' => 'https://example.test/', 'handler' => $stack]);

        $actual = (new Unit($client))->post(byApiKey('test-key'), ['title' => 'test', 'status' => 'active']);

        self::assertSame($response, $actual);
        self::assertCount(1, $history);
        $request = $history[0]['request'];
        self::assertSame('POST', $request->getMethod());
        self::assertSame('https://example.test/rest/api/Unit', (string)$request->getUri());
        self::assertSame('test-key', $request->getHeaderLine('X-REST-API-KEY'));
        self::assertSame('application/json', $request->getHeaderLine('Content-Type'));
        self::assertSame(['title' => 'test', 'status' => 'active'], json_decode((string)$request->getBody(), true));
    }

    public function testBodyIsForwardedWithoutClientSideSchemaValidation(): void
    {
        $history = [];
        $stack = HandlerStack::create(new MockHandler([new Response(200)]));
        $stack->push(Middleware::history($history));
        $client = new Client(['handler' => $stack]);

        (new Unit($client))->post(byApiKey('test-key'), ['unknown_field' => null]);

        self::assertSame(['unknown_field' => null], json_decode((string)$history[0]['request']->getBody(), true));
    }

    public function testPutSendsJsonToExactIdAndReturnsOriginalResponse(): void
    {
        $response = new Response(201, [], '{"success":true}');
        $history = [];
        $stack = HandlerStack::create(new MockHandler([$response]));
        $stack->push(Middleware::history($history));
        $client = new Client(['base_uri' => 'https://example.test/', 'handler' => $stack]);

        $actual = (new Unit($client))->put(byApiKey('test-key'), 50, ['title' => 'technical']);

        self::assertSame($response, $actual);
        self::assertCount(1, $history);
        $request = $history[0]['request'];
        self::assertSame('PUT', $request->getMethod());
        self::assertSame('https://example.test/rest/api/Unit/50', (string)$request->getUri());
        self::assertSame('test-key', $request->getHeaderLine('X-REST-API-KEY'));
        self::assertSame('application/json', $request->getHeaderLine('Content-Type'));
        self::assertSame(['title' => 'technical'], json_decode((string)$request->getBody(), true));
    }

    public function testHttpErrorsConfigurationIsPreserved(): void
    {
        $throwing = new Client(['handler' => HandlerStack::create(new MockHandler([new Response(422)]))]);
        try {
            (new Unit($throwing))->post(byApiKey('test-key'), ['title' => 'test']);
            self::fail('Expected the Guzzle exception');
        } catch (ClientException $exception) {
            self::assertSame(422, $exception->getResponse()->getStatusCode());
        }

        $returning = new Client([
            'handler' => HandlerStack::create(new MockHandler([new Response(500)])),
            'http_errors' => false,
        ]);
        self::assertSame(500, (new Unit($returning))->post(byApiKey('test-key'), [])->getStatusCode());
    }

    public function testPutPreservesGuzzleHttpErrorBehavior(): void
    {
        $throwing = new Client(['handler' => HandlerStack::create(new MockHandler([new Response(406)]))]);
        try {
            (new Unit($throwing))->put(byApiKey('test-key'), 50, ['title' => null]);
            self::fail('Expected the Guzzle exception');
        } catch (ClientException $exception) {
            self::assertSame(406, $exception->getResponse()->getStatusCode());
        }

        $returning = new Client([
            'handler' => HandlerStack::create(new MockHandler([new Response(406)])),
            'http_errors' => false,
        ]);
        self::assertSame(406, (new Unit($returning))->put(byApiKey('test-key'), 50, [])->getStatusCode());
    }

    public function testDeleteSendsOnlyHeadersToExactIdAndReturnsOriginalResponse(): void
    {
        $response = new Response(200, [], '{"success":true}');
        $history = [];
        $stack = HandlerStack::create(new MockHandler([$response]));
        $stack->push(Middleware::history($history));
        $client = new Client(['base_uri' => 'https://example.test/', 'handler' => $stack]);

        $actual = (new Unit($client))->delete(byApiKey('test-key'), 50);

        self::assertSame($response, $actual);
        self::assertCount(1, $history);
        $request = $history[0]['request'];
        self::assertSame('DELETE', $request->getMethod());
        self::assertSame('https://example.test/rest/api/Unit/50', (string)$request->getUri());
        self::assertSame('test-key', $request->getHeaderLine('X-REST-API-KEY'));
        self::assertSame('', (string)$request->getBody());
        self::assertFalse($request->hasHeader('Content-Type'));
    }

    public function testDeletePreservesGuzzleHttpErrorBehavior(): void
    {
        $throwing = new Client(['handler' => HandlerStack::create(new MockHandler([new Response(404)]))]);
        try {
            (new Unit($throwing))->delete(byApiKey('test-key'), 50);
            self::fail('Expected the Guzzle exception');
        } catch (ClientException $exception) {
            self::assertSame(404, $exception->getResponse()->getStatusCode());
        }

        $returning = new Client([
            'handler' => HandlerStack::create(new MockHandler([new Response(404)])),
            'http_errors' => false,
        ]);
        self::assertSame(404, (new Unit($returning))->delete(byApiKey('test-key'), 50)->getStatusCode());
    }
}
