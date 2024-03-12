<?php

namespace Kedniko\Vivy\Core;

use Kedniko\Vivy\V;

final class Validated
{
    public $chain;

    public function __construct(private readonly mixed $value, private $errors)
    {
    }

    private function fail(callable $handler): mixed
    {
        return $handler($this);
    }

    private function getFailHandler(string|callable $handler): callable
    {
        if (is_string($handler)) {
            $handler = $this->chain->getSetup()->failHandlers[$handler] ?? V::getGlobalFailHandler($handler);
        }

        return $handler;
    }

    public function forceFailWith(string|callable $handler)
    {
        $handler = $this->getFailHandler($handler);

        return $this->fail($handler);
    }

    /**
     * Handler can be set with `V::setFailHandler()`
     */
    public function orFailWith(string|callable $handler): Validated
    {
        if ($this->fails()) {
            $handler = $this->getFailHandler($handler);

            $this->fail($handler);
        }

        return $this;
    }

    public function fails(): bool
    {
        return $this->errors && (is_countable($this->errors) ? count($this->errors) : 0);
    }

    public function isValid(): bool
    {
        return (is_countable($this->errors) ? count($this->errors) : 0) === 0;
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function value(): mixed
    {
        return $this->value;
    }
}
