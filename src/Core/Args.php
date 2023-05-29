<?php

namespace Kedniko\Vivy\Core;

class Args
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
