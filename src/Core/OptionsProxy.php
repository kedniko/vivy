<?php

namespace Kedniko\Vivy\Core;

final class OptionsProxy extends Options
{
    public $builder;
    public function __construct($builder)
    {
        $this->builder = $builder;
    }

    public function setBuilder($builder): void
    {
        $this->builder = $builder;
    }
}
