<?php

namespace Kedniko\Vivy\Core;

use Kedniko\Vivy\Exceptions\VivyValidationFailedException;
use Kedniko\Vivy\V;

final class Validated
{
    /**
     * @var mixed|Ref
     */
    private $value;

    /**
     * @var array|Ref
     */
    private $errors;

    public $chain;

    private const OR_FAIL = false;

    /**
     * @param  mixed  $value
     * @param  array  $errors
     */
    public function __construct($value, $errors)
    {
        $this->value = $value;
        $this->errors = $errors;
    }

    /**
     * @param  callable  $handler
     */
    private function fail($handler)
    {
        if ($handler && is_callable($handler)) {
            return $handler($this);
        } elseif (V::getFailHandler()) {
            $handler = V::getFailHandler();

            return $handler($this);
        } else {
            throw new VivyValidationFailedException();
        }
    }

    /**
     * @param  string|callable  $handler
     */
    public function forceFailWith($handler)
    {
        if (is_string($handler)) {
            $handler = V::getFailHandler($handler);
        }

        return $this->fail($handler);
    }

    /**
     * Handler can be set with `V::setFailHandler()`
     *
     * @param  string|callable  $handler
     */
    public function orFailWith($handler)
    {
        if ($this->fails()) {
            if (is_string($handler)) {
                $handler = V::getFailHandler($handler);
            }
            $this->fail($handler);
        }

        return $this;
    }

    public function fails()
    {
        return $this->errors && count($this->errors);
    }

    public function isValid()
    {
        $errors = $this->errors;

        return count($errors) === 0;
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
