<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi;

use PHPUnit\Framework\TestCase;

class ModelTest extends TestCase
{
    public function testAsStringWithValidModel(): void
    {
        $this->assertEquals(
            'invoice',
            (
            new Model('invoice')
            )->asString()
        );
    }

    public function testAsStringWithInvalidModel(): void
    {
        $this->expectException(\Exception::class);
        (new Model('invalidModel'))->asString();
    }
}
