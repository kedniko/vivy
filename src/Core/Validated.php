<?php

namespace Kedniko\Vivy\Core;

use Kedniko\Vivy\Exceptions\VivyValidationFailedException;
use Kedniko\Vivy\V;

final class Validated
{
    public $chain;

    /**
     * @param  array  $errors
     */
    public function __construct(private readonly mixed $value, private $errors)
    {
    }

    /**
     * @param  callable  $handler
     */
    private function fail($handler)
    {
        if ($handler && is_callable($handler)) {
            return $handler($this);
        }
        if (V::getFailHandler()) {
            $handler = V::getFailHandler();
            return $handler($this);
        }
        else {
            throw new VivyValidationFailedException();
        }
    }

    public function forceFailWith(string|callable $handler)
    {
        if (is_string($handler)) {
            $handler = V::getFailHandler($handler);
        }

        return $this->fail($handler);
    }

    /**
     * Handler can be set with `V::setFailHandler()`
     */
    public function orFailWith(string|callable $handler)
    {
        if ($this->fails()) {
            if (is_string($handler)) {
                $handler = V::getFailHandler($handler);
            }
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
        $errors = $this->errors;

        return (is_countable($errors) ? count($errors) : 0) === 0;
    }

    public function errors()
    {
        // if ($this->errors instanceof Ref) {
        //     return $this->errors->value();
        // }

        return $this->errors;
    }

    public function value()
    {
        // if ($this->value instanceof Ref) {
        //     return $this->value->value();
        // }

        return $this->value;
    }
}
