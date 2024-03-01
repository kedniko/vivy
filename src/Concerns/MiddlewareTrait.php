<?php

namespace Kedniko\Vivy\Concerns;

use Kedniko\Vivy\Core\Options;
use Kedniko\Vivy\Core\Rule;
use Kedniko\Vivy\Exceptions\VivyException;
use Kedniko\Vivy\Messages\RuleMessage;
use Kedniko\Vivy\O;

trait MiddlewareTrait
{
    protected string $id;

    protected Options $options;

    protected mixed $errormessage;

    protected bool $stopOnFailure;

    // protected $args;
    /**
     * @param  string  $id
     * @param  callable|null  $callback
     */
    public function __construct(
        $id,
        protected $callback,
        string|callable $errormessage = null
    ) {
        if ($errormessage === null) {
            $errormessage = RuleMessage::getErrorMessage();
        }
        if (!is_scalar($id)) {
            throw new VivyException('Middleware ID must be a scalar value');
        }
        $this->id = $id;
        $this->options = O::options();
        $this->options->message($errormessage);
        $this->options->setArgs([]);
    }

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
        return $this->options->getErrorMessage();
    }

    public function setErrorMessage($errmessage)
    {
        $this->options->message($errmessage);

        return $this;
    }

    public function setStopOnFailure($stopOnFailure)
    {
        $this->options->stopOnFailure($stopOnFailure);

        return $this;
    }

    public function getStopOnFailure()
    {
        return $this->options->getStopOnFailure();
    }

    public function getArgs(): array
    {
        return $this->options->getArgs();
    }

    public function isRule(): bool
    {
        return $this instanceof Rule;
    }

    public function setArgs(array $args)
    {
        $this->options->setArgs($args);

        return $this;
    }
    // public function enableToEach()
    // {
    // 	$this->each = true;
    // 	return $this;
    // }

    public function getOptions(): Options
    {
        return $this->options;
    }

    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }
}
