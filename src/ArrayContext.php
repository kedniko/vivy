<?php

namespace Kedniko\Vivy;

final class ArrayContext extends Context
{
    public $index = null;

    public $failsCount = null;

    public $successCount = null;

    public function __construct(Context $c = null)
    {
        parent::__construct($c);
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
}
