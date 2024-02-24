<?php

namespace Kedniko\Vivy\Core;


final class Options
{
    private bool $stopOnFailure = true;

    private $errormessage;

    private bool $once = false;

    private $if;

    private ?bool $appendAfterCurrent = null;

    private array $args = [];

    /**
     * @var Type
     */
    private $builder;

    /**
     * @param  null|string  $errormessage
     * @param  bool  $stopOnFailure
     */
    public function __construct()
    {
        $this->if = Undefined::instance();
    }

    public static function build(Options $options = null, array $args = [], string $errormessage = null)
    {
        if (!($options instanceof Options)) {
            $options = new Options();
        }
        $options->setArgs($args); // TODO: not implemented

        return $options;
    }

    public function message(string|callable $errormessage = null)
    {
        $this->errormessage = $errormessage;

        return $this;
    }

    /**
     * Do not pass context to the callback to improve performance
     */
    public function ignoreContext()
    {
        return $this;
    }

    /**
     * Remove this rule after it has been used
     */
    public function once()
    {
        $this->once = true;

        return $this;
    }

    public function appendAfterCurrent()
    {
        $this->appendAfterCurrent = true;

        return $this;
    }

    public function continueOnFailure()
    {
        $this->stopOnFailure = false;

        return $this;
    }

    // TODO
    public function stopOnSuccess()
    {
        return $this;
    }

    public function stopOnFailure()
    {
        $this->stopOnFailure = true;

        return $this;
    }

    // public function setStopOnFailure(bool $bool)
    // {
    // 	$this->stopOnFailure = $bool;
    // 	return $this;
    // }

    public function getArgs(): array
    {
        return $this->args;
    }

    public function setArgs(array $args)
    {
        $this->args = array_filter($args, fn ($arg): bool => !($arg instanceof Options));

        return $this;
    }

    /**
     * @param  array  $args
     */
    public function addArgs($args)
    {
        foreach ($args as $arg) {
            $this->args[] = $arg;
        }

        return $this;
    }

    /**
     * Get the value of stopOnFailure
     */
    public function getStopOnFailure(): bool
    {
        return $this->stopOnFailure;
    }

    /**
     * Get the value of errormessage
     *
     * @return  string
     */
    public function getErrorMessage()
    {
        return $this->errormessage;
    }

    /**
     * Get the value of consume
     */
    public function getOnce(): bool
    {
        return $this->once;
    }

    /**
     * Get the value of appendAfterCurrent
     */
    public function getAppendAfterCurrent(): ?bool
    {
        return $this->appendAfterCurrent;
    }

    public function hasIf()
    {
        return !($this->if instanceof Undefined);
    }

    /**
     * Get the value of if
     */
    public function getIf()
    {
        return $this->if;
    }

    /**
     * Set the value of if
     *
     * @param  callable  $callback function(Context $context) {...}
     * @return  self
     */
    public function ifRule($if)
    {
        $this->if = $if;

        return $this;
    }

    // CHECKME
    public function getBuilder()
    {
        return $this->builder;
    }
}
