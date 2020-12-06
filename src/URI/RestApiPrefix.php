<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\URI;

use ElegantBro\Interfaces\Stringify;

final class RestApiPrefix implements Stringify
{
    public function asString(): string
    {
        return '/rest/api/';
    }
}
