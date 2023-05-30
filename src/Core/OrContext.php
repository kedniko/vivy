<?php

namespace Kedniko\Vivy\Core;

use Kedniko\Vivy\Context;

final class OrContext extends Context
{
    private $childErrors;

    public function __construct($childErrors, Context $c = null)
    {
        parent::__construct($c);
        $this->childErrors = $childErrors;
    }

    public function childErrors()
    {
        return $this->childErrors;
    }
}
