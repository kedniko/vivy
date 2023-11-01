<?php

namespace App;

class Token
{
    public function __construct(public bool $expired = false)
    {
        //
    }
}
