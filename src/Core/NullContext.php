<?php

namespace Kedniko\Vivy\Core;

use Kedniko\Vivy\Concerns\ContextTrait;
use Kedniko\Vivy\Contracts\ContextInterface;

final class NullContext implements ContextInterface
{
    use ContextTrait;

    public function __construct(ContextInterface $cloneFrom = null, ContextInterface $fatherContext = null)
    {
        $this->init($cloneFrom, $fatherContext);
    }
}
