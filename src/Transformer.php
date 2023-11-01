<?php

namespace Kedniko\Vivy;

use Kedniko\Vivy\Concerns\MiddlewareTrait;
use Kedniko\Vivy\Contracts\MiddlewareInterface;

final class Transformer implements MiddlewareInterface
{
    use MiddlewareTrait;

    public function getID(): string
    {
        return $this->id;
    }

    public function getCallback()
    {
        return $this->callback;
    }

    public function getErrorMessage()
    {
        return $this->errormessage;
    }

    public function setErrorMessage(string $errmessage): void
    {
        $this->errormessage = $errmessage;
    }

    public function setStopOnFailure(bool $stopOnFailure): void
    {
        $this->stopOnFailure = $stopOnFailure;
    }

    public function getStopOnFailure(): bool
    {
        return $this->stopOnFailure;
    }
}
