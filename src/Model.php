<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi;

use ElegantBro\Interfaces\Stringify;

final class Model implements Stringify
{
    /**
     * @var string
     */
    private $model;
    /**
     * @var string[]
     */
    private $validModels = [
        'invoice',
        'client',
        'user'
    ];

    /**
     * Model constructor.
     * @param string $model
     */
    public function __construct(string $model)
    {
        $this->model = $model;
    }

    /**
     * @inheritDoc
     */
    public function asString(): string
    {
        if (!in_array($this->model, $this->validModels)) {
            throw new \InvalidArgumentException("Model is not valid");
        }
        return $this->model;
    }
}
