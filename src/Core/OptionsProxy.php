<?php

namespace Kedniko\Vivy\Core;

final class OptionsProxy extends Options
{
    public function __construct(public $builder)
    {
    }

    public function setBuilder($builder): void
    {
        $this->builder = $builder;
    }
}
