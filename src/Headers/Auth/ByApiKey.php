<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\Headers\Auth;

use Otis22\VetmanagerRestApi\Headers\Auth;

final class ByApiKey implements Auth
{
    /**
     * @var ApiKey
     */
    private $apiKey;

    /**
     * ApiKeyAuth constructor.
     * @param ApiKey $apiKey
     */
    public function __construct(ApiKey $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @inheritDoc
     */
    public function asKeyValue(): array
    {
        return [
            'X-REST-API-KEY' => $this->apiKey->asString()
        ];
    }
}
