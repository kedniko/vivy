<?php

namespace Kedniko\Vivy;

use Kedniko\Vivy\Core\Middleware;

final class Transformer extends Middleware
{
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

    public function setErrorMessage($errmessage)
    {
        $this->errormessage = $errmessage;
    }

    public function setStopOnFailure($stopOnFailure)
    {
        $this->stopOnFailure = $stopOnFailure;
    }

    public function getStopOnFailure()
    {
        return $this->stopOnFailure !== false;
    }
}
