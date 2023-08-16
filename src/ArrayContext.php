<?php

namespace Kedniko\Vivy;

use Kedniko\Vivy\Concerns\ContextTrait;
use Kedniko\Vivy\Contracts\Context;

final class ArrayContext implements Context
{
    use ContextTrait;

    public $index;

    public $failsCount;

    public $successCount;

    public function __construct(Context $cloneFrom = null, Context $fatherContext = null)
    {
        $this->init($cloneFrom, $fatherContext);
    }

    public function getIndex()
    {
        return $this->index;
    }

    public function getFailsCount()
    {
        return $this->failsCount;
    }

    public function getSuccessCount()
    {
        return $this->successCount;
    }


    public function setIndex($value)
    {
        $this->index = $value;

        return $this;
    }

    public function setFailsCount($value)
    {
        $this->failCount = $value;

        return $this;
    }

    public function setSuccessCount($value)
    {
        $this->successCount = $value;

        return $this;
    }
}
