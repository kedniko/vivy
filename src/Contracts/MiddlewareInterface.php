<?php

namespace Kedniko\Vivy\Contracts;

use Kedniko\Vivy\Core\Options;

interface MiddlewareInterface
{
    public function getID();

    public function getCallback();

    public function getErrorMessage();

    public function setErrorMessage(string $errmessage);

    public function setStopOnFailure(bool $stopOnFailure);

    public function getStopOnFailure();

    public function getArgs();

    public function isRule();

    public function setArgs(array $args);

    public function getOptions();

    public function setOptions(Options $options);
}
