<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\URI;

use PHPUnit\Framework\TestCase;

use function Otis22\VetmanagerRestApi\openApiPath;
use function Otis22\VetmanagerRestApi\uri;

class OpenApiPathTest extends TestCase
{
    public function testStaticAndParameterizedRoutes(): void
    {
        $this->assertSame('/rest/api/messages/all', openApiPath('messages/all')->asString());
        $this->assertSame(
            '/rest/api/VmLink/personalAccountLinkByPhone/%2F%3F%23%252F',
            openApiPath('VmLink/personalAccountLinkByPhone/{phone}', ['phone' => '/?#%2F'])->asString()
        );
        $this->assertSame(
            '/rest/api/VmLink/personalAccountLinkByPhone/%D0%BA%D0%BE%D1%82',
            openApiPath('VmLink/personalAccountLinkByPhone/{phone}', ['phone' => 'кот'])->asString()
        );
        $this->assertSame(
            '/rest/api/report-ai-job/42/confirm',
            openApiPath('report-ai-job/{id}/confirm', ['id' => 42])->asString()
        );
        $this->assertSame('/rest/api/invoice/7', uri('invoice', 7)->asString());
    }

    /**
     * @dataProvider invalidRoutes
     * @param array<string, mixed> $parameters
     */
    public function testRejectsInvalidRoute(string $template, array $parameters): void
    {
        $this->expectException(\InvalidArgumentException::class);
        openApiPath($template, $parameters);
    }

    /** @return array<int, array{string, array<string, mixed>}> */
    public function invalidRoutes(): array
    {
        return [
            ['../invoice', []],
            ['messages/all?x=1', []],
            ['messages/all', ['extra' => 'x']],
            ['report-ai-job/{id}/confirm', []],
            ['report-ai-job/{id}/confirm', ['ID' => 1]],
            ['report-ai-job/{id}/confirm', ['id' => '']],
            ['report-ai-job/{id}/confirm', ['id' => '.']],
            ['report-ai-job/{id}/confirm', ['id' => '..']],
            ['report-ai-job/{id}/confirm', ['id' => []]],
        ];
    }
}
