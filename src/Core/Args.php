<?php

namespace Kedniko\Vivy\Core;

final class Args
{
    /**
     * @var array
     */
    public $args;

    public function __construct($args = [])
    {
        $this->args = $args;
    }
}
