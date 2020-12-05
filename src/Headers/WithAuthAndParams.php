<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\Headers;

use Otis22\VetmanagerRestApi\Headers;
use ElegantBro\Arrayee\Just;
use ElegantBro\Arrayee\Merged;

final class WithAuthAndParams implements Headers
{
    /**
     * @var Auth
     */
    private $auth;
    /**
     * @var array<string, string>
     */
    private $params;

    /**
     * WithAuthAndParams constructor.
     * @param Auth $auth
     * @param string[] $params
     */
    public function __construct(Auth $auth, array $params)
    {
        $this->auth = $auth;
        $this->params = $params;
    }

    /**
     * @inheritDoc
     */
    public function asKeyValue(): array
    {
        return (
            new Merged(
                new Just($this->auth->asKeyValue()),
                new Just($this->params)
            )
        )->asArray();
    }
}
