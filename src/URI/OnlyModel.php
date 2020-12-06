<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\URI;

use Exception;
use Otis22\VetmanagerRestApi\Model;
use Otis22\VetmanagerRestApi\URI;

final class OnlyModel implements URI
{
    /**
     * @var Model
     */
    private $model;

    /**
     * OnlyModel constructor.
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function asString(): string
    {
        return (new RestApiPrefix())->asString()
            . $this->model->asString();
    }
}
