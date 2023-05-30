<?php

namespace Kedniko\Vivy\Core;

final class FieldMiddleware extends Middleware
{
    public function __construct(private $type)
    {
    }

    public function getField()
    {
        return $this->type;
    }
}
