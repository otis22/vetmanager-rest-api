<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\Headers\Auth;

use Otis22\VetmanagerRestApi\Headers\Auth;

final class ByServiceApiKey implements Auth
{
    private ServiceName $serviceName;
    private ApiKey $apiKey;

    /**
     * @param ServiceName $serviceName
     * @param ApiKey $apiKey
     */
    public function __construct(ServiceName $serviceName, ApiKey $apiKey)
    {
        $this->serviceName = $serviceName;
        $this->apiKey = $apiKey;
    }

    /**
     * @inheritDoc
     */
    public function asKeyValue(): array
    {
        return [
            'X-SERVICE-REST-API-KEY' => $this->apiKey->asString(),
            'X-SERVICE-NAME' => $this->serviceName->asString()
        ];
    }
}
