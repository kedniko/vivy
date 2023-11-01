<?php

namespace Kedniko\Vivy;

use Kedniko\Vivy\Concerns\MiddlewareTrait;
use Kedniko\Vivy\Contracts\MiddlewareInterface;

final class Callback implements MiddlewareInterface
{
    use MiddlewareTrait;

    public function getID(): string
    {
        return $this->id;
    }

    public function getCallback(): ?callable
    {
        return $this->callback;
    }

    public function getErrorMessage(): string
    {
        return $this->errormessage;
    }

    public function setErrorMessage(mixed $errmessage): void
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
