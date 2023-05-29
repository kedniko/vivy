<?php

namespace Kedniko\Vivy\Core;

use Kedniko\Vivy\Exceptions\VivyException;
use Kedniko\Vivy\Messages\RuleMessage;
use Kedniko\Vivy\O;

class Middleware
{
    protected $id;

    protected $options;

    protected $callback;

    protected $errormessage;

    protected $stopOnFailure;
    // protected $args;

    /**
     * @param  string  $id
     * @param  callable|null  $callback
     * @param  string|callable|null  $errormessage
     */
    public function __construct($id, $callback, $errormessage = null)
    {
        if ($errormessage === null) {
            $errormessage = RuleMessage::getErrorMessage();
        }
        if (! is_scalar($id)) {
            throw new VivyException('Middleware ID must be a scalar value');
        }
        $this->id = $id;
        $this->options = O::options();
        $this->callback = $callback;
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

    public function getArgs()
    {
        return $this->options->getArgs();
    }

    public function isRule()
    {
        return $this instanceof Rule;
    }

    /**
     * @param  array  $args
     */
    public function setArgs($args)
    {
        $this->options->setArgs($args);

        return $this;
    }
    // public function enableToEach()
    // {
    // 	$this->each = true;
    // 	return $this;
    // }

    /**
     * Get the value of options
     *
     * @return Options
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set the value of options
     *
     * @return  self
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }
}
