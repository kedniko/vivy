<?php

namespace Kedniko\Vivy;

final class ArrayContext extends Context
{
    public $index;

    public $failsCount;

    public $successCount;

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
