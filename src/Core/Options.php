<?php

namespace Kedniko\Vivy\Core;

use Closure;
use Kedniko\Vivy\Contracts\TypeInterface;
use Kedniko\Vivy\Support\Util;

final class Options
{
    private bool $stopOnFailure = true;

    private string|Closure|null $errormessage = null;

    private bool $once = false;

    private Closure|Undefined $if;

    private ?bool $appendAfterCurrent = null;

    private array $args = [];

    private ?string $functionName = null;

    private ?TypeInterface $builder = null;

    public function __construct()
    {
        $this->if = Undefined::instance();
    }

    public static function build(?Options $options = null, array $args = [], ?string $fn = null)
    {

        if (! ($options instanceof Options)) {
            $options = new Options();
        }

        $options->setArgs($args);
        $fnName = Util::getFunctionName($fn);
        assert(is_string($fnName) && ! empty($fnName));
        $options->setFunctionName($fnName);

        return $options;
    }

    public function message(string|Closure|null $errormessage = null)
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
        $this->args = array_filter($args, fn ($arg): bool => ! ($arg instanceof Options));

        return $this;
    }

    public function getFunctionName(): string
    {
        return $this->functionName;
    }

    public function setFunctionName(string $name)
    {
        $this->functionName = $name;

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
     * @return string
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
        return ! ($this->if instanceof Undefined);
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
     * @param  callable  $callback  function(Context $context) {...}
     * @return self
     */
    public function ifRule(callable $if)
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
