<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\URI;

use Otis22\VetmanagerRestApi\Model;
use PHPUnit\Framework\TestCase;

class OnlyModelTest extends TestCase
{
    public function testAsString(): void
    {
        $this->assertTrue(
            strpos(
                (
                    new OnlyModel(
                        new Model('invoice')
                    )
                )->asString(),
                'invoice'
            ) !== false
        );
    }
}
