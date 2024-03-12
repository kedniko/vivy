<?php

namespace Kedniko\Vivy\Core;

use Kedniko\Vivy\Concerns\ContextTrait;
use Kedniko\Vivy\Contracts\ContextInterface;

final class OrContext implements ContextInterface
{
    use ContextTrait;

    private function __construct(
        public array $childErrors = [],
        ?ContextInterface $cloneFrom = null,
        ?ContextInterface $fatherContext = null
    ) {
        $this->init($cloneFrom, $fatherContext);
    }

    public static function new(
        array $childErrors,
        ?ContextInterface $cloneFrom = null,
        ?ContextInterface $fatherContext = null
    ) {
        $oc = new OrContext(
            $childErrors,
            $cloneFrom,
            $fatherContext
        );
        $oc->init($cloneFrom, $fatherContext);

        return $oc;
    }

    public function getChildErrors()
    {
        return $this->childErrors;
    }
}
