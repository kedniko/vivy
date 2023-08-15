<?php

namespace Kedniko\Vivy\Core;

use Kedniko\Vivy\Concerns\MiddlewareTrait;
use Kedniko\Vivy\Contracts\MiddlewareInterface;

final class FieldMiddleware implements MiddlewareInterface
{
    use MiddlewareTrait;

    public function __construct(private $type)
    {
    }

    public function getField()
    {
        return $this->type;
    }
}
