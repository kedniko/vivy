<?php

namespace Kedniko\Vivy;

use Kedniko\Vivy\Core\Middleware;
use Kedniko\Vivy\Concerns\MiddlewareTrait;
use Kedniko\Vivy\Contracts\MiddlewareInterface;

final class Callback implements MiddlewareInterface
{
    use MiddlewareTrait;

    public function getID()
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

    public function setErrorMessage($errmessage): void
    {
        $this->errormessage = $errmessage;
    }

    public function setStopOnFailure($stopOnFailure): void
    {
        $this->stopOnFailure = $stopOnFailure;
    }

    public function getStopOnFailure()
    {
        return $this->stopOnFailure;
    }
}
