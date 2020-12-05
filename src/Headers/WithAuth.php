<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\Headers;

use Otis22\VetmanagerRestApi\Headers;

final class WithAuth implements Headers
{
    /**
     * @var Auth
     */
    protected $auth;

    /**
     * WithAuth constructor.
     * @param Auth $auth
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * @inheritDoc
     */
    public function asKeyValue(): array
    {
        return $this->auth->asKeyValue();
    }
}
