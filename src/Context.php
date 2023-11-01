<?php

namespace Kedniko\Vivy;

use Kedniko\Vivy\Concerns\ContextTrait;
use Kedniko\Vivy\Contracts\ContextInterface as ContractsContext;

final class Context implements ContractsContext
{
    use ContextTrait;

    public function __construct(ContractsContext $cloneFrom = null, ContractsContext $fatherContext = null)
    {
        $this->init($cloneFrom, $fatherContext);
    }
}
