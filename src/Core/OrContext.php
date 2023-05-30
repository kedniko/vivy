<?php

namespace Kedniko\Vivy\Core;

use Kedniko\Vivy\Context;

final class OrContext extends Context
{
    public function __construct(private $childErrors, Context $c = null)
    {
        parent::__construct($c);
    }

    public function childErrors()
    {
        return $this->childErrors;
    }
}
