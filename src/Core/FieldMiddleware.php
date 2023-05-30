<?php

namespace Kedniko\Vivy\Core;

final class FieldMiddleware extends Middleware
{
    private $type;

    public function __construct($type)
    {
        $this->type = $type;
    }

    public function getField()
    {
        return $this->type;
    }
}
