<?php

namespace App;

class StringRule
{
    public function validate(): bool
    {
        return true;
    }

    public static function make()
    {
        return new StringRule;
    }
}
