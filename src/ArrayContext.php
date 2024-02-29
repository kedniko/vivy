<?php

namespace Kedniko\Vivy;

use Kedniko\Vivy\Concerns\ContextTrait;
use Kedniko\Vivy\Contracts\ContextInterface;

final class ArrayContext implements ContextInterface
{
    use ContextTrait;

    public $index;

    public ?int $failsCount = null;

    public $successCount;

    public function __construct(ContextInterface $cloneFrom = null, ContextInterface $fatherContext = null)
    {
        $this->init($cloneFrom, $fatherContext);
    }

    public function getIndex()
    {
        return $this->index;
    }

    public function getFailsCount(): int
    {
        return $this->failsCount;
    }

    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    public function setIndex($value)
    {
        $this->index = $value;

        return $this;
    }

    public function setFailsCount(int $value): ArrayContext
    {
        $this->failsCount = $value;

        return $this;
    }

    public function setSuccessCount(int $value): ArrayContext
    {
        $this->successCount = $value;

        return $this;
    }
}
