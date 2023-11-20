<?php

namespace App\Rules;

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
