<?php

namespace Otis22\VetmanagerRestApi;

use Otis22\VetmanagerToken\Credentials\AppName;
use Otis22\VetmanagerToken\Token;
use Otis22\VetmanagerRestApi\Headers\Auth;
use Otis22\VetmanagerRestApi\Headers\WithAuth;
use Otis22\VetmanagerRestApi\URI;

/**
 * Function for creating auth headers <code>byToken($appName, $token)->asKeyValue()</code>
 * @param string $appName
 * @param string $token
 * @return Headers
 */
function byToken(string $appName, string $token): Headers
{
    return new WithAuth(
        new Auth\ByToken(
            new AppName($appName),
            new Token\Concrete($token)
        )
    );
}

/**
 * Function for creating auth headers <code>byApiKey($apiKey)->asKeyValue()</code>
 * @param string $apiKey
 * @return Headers
 */
function byApiKey(string $apiKey): Headers
{
    return new WithAuth(
        new Auth\ByApiKey(
            new Auth\ApiKey($apiKey)
        )
    );
}

/**
 * Function for creating auth headers for services by api key <code>byApiKey($serviceName, $apiKey)->asKeyValue()</code>
 * @param string $serviceName
 * @param string $apiKey
 * @return Headers
 */
function byServiceApiKey(string $serviceName, string $apiKey): Headers
{
    return new WithAuth(
        new Auth\ByServiceApiKey(
            new Auth\ServiceName($serviceName),
            new Auth\ApiKey($apiKey)
        )
    );
}

/**
 * Function return uri by model name and id entity
 * @param string $model
 * @param int|null $id
 * @return URI
 */
function uri(string $model, ?int $id = null): URI
{
    return is_null($id)
        ? new URI\OnlyModel(new Model($model))
        : new URI\WithId(new Model($model), $id);
}
