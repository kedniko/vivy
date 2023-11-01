<?php

namespace Kedniko\Vivy\Core;

use Kedniko\Vivy\Concerns\ContextTrait;
use Kedniko\Vivy\Contracts\ContextInterface;

final class OrContext implements ContextInterface
{
    use ContextTrait;

    /**
     * TODO: childErrors is not used
     *
     * @param  private  $childErrors
     * @param  ContextInterface|null  $c
     */
    public function __construct(private $childErrors, ContextInterface $cloneFrom = null, ContextInterface $fatherContext = null)
    {
        $this->init($cloneFrom, $fatherContext);
    }

    public function childErrors()
    {
        return $this->childErrors;
    }
}
