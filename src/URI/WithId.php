<?php

declare(strict_types=1);

namespace Otis22\VetmanagerRestApi\URI;

use ElegantBro\Interfaces\Stringify;
use Otis22\VetmanagerRestApi\Model;

final class WithId implements Stringify
{
    /**
     * @var Model
     */
    private $model;
    /**
     * @var int
     */
    private $id;

    /**
     * WithId constructor.
     * @param Model $model
     * @param int $id
     */
    public function __construct(Model $model, int $id)
    {
        $this->model = $model;
        $this->id = $id;
    }

    public function asString(): string
    {
        return (new RestApiPrefix())->asString()
            . $this->model->asString()
            . "/"
            . $this->id;
    }
}
