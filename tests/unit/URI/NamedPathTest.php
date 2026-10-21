<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\URI;

use PHPUnit\Framework\TestCase;

use function Otis22\VetmanagerRestApi\namedPath;
use function Otis22\VetmanagerRestApi\uri;

class NamedPathTest extends TestCase
{
    public function testStaticAndParameterizedRoutes(): void
    {
        $this->assertSame('/rest/api/messages/all', namedPath('messages/all')->asString());
        $this->assertSame(
            '/rest/api/VmLink/personalAccountLinkByPhone/%2F%3F%23%252F',
            namedPath('VmLink/personalAccountLinkByPhone/{phone}', ['phone' => '/?#%2F'])->asString()
        );
        $this->assertSame(
            '/rest/api/VmLink/personalAccountLinkByPhone/%D0%BA%D0%BE%D1%82',
            namedPath('VmLink/personalAccountLinkByPhone/{phone}', ['phone' => 'кот'])->asString()
        );
        $this->assertSame(
            '/rest/api/report-ai-job/42/confirm',
            namedPath('report-ai-job/{id}/confirm', ['id' => 42])->asString()
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
        namedPath($template, $parameters);
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
