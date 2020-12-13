<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\Query\Json;

use ElegantBro\Arrayee\Just;
use Otis22\PhpInterfaces\KeyValue;
use PHPUnit\Framework\TestCase;

class EncodedMappedKeyValueArrayTest extends TestCase
{

    public function testAsString(): void
    {
        $this->assertEquals(
            '[{"sort":"by"}]',
            (
                new EncodedMappedKeyValueArray(
                    new Just(
                        [
                            new class implements KeyValue {
                                public function asKeyValue(): array
                                {
                                    return ['sort' => 'by'];
                                }
                            }
                        ]
                    )
                )
            )->asString()
        );
    }
}
