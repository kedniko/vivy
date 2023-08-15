<?php

namespace Kedniko\Vivy\Core;

use Kedniko\Vivy\Context;

final class ContextProxy extends Context
{
    public $field;

    /**
     * @param  Context  $context
     */
    public function __construct(private $context)
    {
    }

    public function setField($type)
    {
        $this->context->type = $type;

        return $this;
    }

    public function setIndex($value)
    {
        $this->context->index = $value;

        return $this;
    }

    public function setFailsCount($value)
    {
        $this->context->failCount = $value;

        return $this;
    }

    public function setSuccessCount($value)
    {
        $this->context->successCount = $value;

        return $this;
    }

    public function setExtra($index, $value)
    {
        $this->context->extra[$index] = $value;

        return $this;
    }

    public function getRawField()
    {
        return $this->context->type;
    }
}
