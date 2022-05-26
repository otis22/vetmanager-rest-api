<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\Headers\Auth;

use ElegantBro\Interfaces\Stringify;

final class ServiceName implements stringify
{
    /**
     * @var string
     */
    private $serviceName;

    /**
     * @param string $serviceName
     */
    public function __construct(string $serviceName)
    {
        $this->serviceName = $serviceName;
    }

    public function asString(): string
    {
        return $this->serviceName;
    }
}
