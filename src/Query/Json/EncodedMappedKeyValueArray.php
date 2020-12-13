<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\Query\Json;

use ElegantBro\Arrayee\Mapped;
use ElegantBro\Interfaces\Arrayee;
use ElegantBro\Interfaces\Stringify;
use ElegantBro\Stringify\Json\JsonEncoded;
use Otis22\PhpInterfaces\KeyValue;

final class EncodedMappedKeyValueArray implements Stringify
{
    /**
     * @var Arrayee
     */
    private $keyValueArray;

    /**
     * EncodedMappedKeyValueArray constructor.
     * @param Arrayee $keyValueArray
     */
    public function __construct(Arrayee $keyValueArray)
    {
        $this->keyValueArray = $keyValueArray;
    }

    /**
     * @inheritDoc
     */
    public function asString(): string
    {
        return JsonEncoded::default(
            (
                new Mapped(
                    $this->keyValueArray,
                    function (KeyValue $keyValue) {
                        return $keyValue->asKeyValue();
                    }
                )
            )->asArray()
        )->asString();
    }
}
