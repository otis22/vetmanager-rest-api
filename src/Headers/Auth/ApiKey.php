<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\Headers\Auth;

use ElegantBro\Interfaces\Stringify;

final class ApiKey implements Stringify
{
    /**
     * @var string
     */
    public $apiKey;

    /**
     * ApiKey constructor.
     * @param string $apiKey
     */
    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @inheritDoc
     */
    public function asString(): string
    {
        return $this->apiKey;
    }
}
