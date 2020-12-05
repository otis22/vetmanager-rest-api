<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\Headers\Auth;

use Otis22\VetmanagerToken\Credentials\AppName;
use Otis22\VetmanagerToken\Token;
use Otis22\VetmanagerRestApi\Headers\Auth;

final class ByToken implements Auth
{
    /**
     * @var AppName
     */
    private $appName;
    /**
     * @var Token
     */
    private $token;

    /**
     * TokenAuth constructor.
     * @param AppName $appName
     * @param Token $token
     */
    public function __construct(AppName $appName, Token $token)
    {
        $this->appName = $appName;
        $this->token = $token;
    }


    /**
     * @inheritDoc
     */
    public function asKeyValue(): array
    {
        return [
            'X-APP-NAME' => $this->appName->asString(),
            'X-USER-TOKEN' => $this->token->asString()
        ];
    }
}
