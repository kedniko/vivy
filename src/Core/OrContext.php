<?php

namespace Kedniko\Vivy\Core;

use Kedniko\Vivy\Concerns\ContextTrait;
use Kedniko\Vivy\Contracts\Context;

final class OrContext implements Context
{

    use ContextTrait;

    /**
     * TODO: childErrors is not used
     * 
     * @param private $childErrors
     * @param Context|null $c
     */
    public function __construct(private $childErrors, Context $cloneFrom = null, Context $fatherContext = null)
    {
        $this->init($cloneFrom, $fatherContext);
    }

    public function childErrors()
    {
        return $this->childErrors;
    }
}
