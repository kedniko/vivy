<?php

namespace Kedniko\Vivy\Concerns;

use Kedniko\Vivy\ArrayContext;
use Kedniko\Vivy\Context;
use Kedniko\Vivy\Contracts\ContextInterface;
use Kedniko\Vivy\Contracts\MiddlewareInterface;
use Kedniko\Vivy\Contracts\TypeInterface;
use Kedniko\Vivy\Core\GroupContext;
use Kedniko\Vivy\Core\Undefined;
use Kedniko\Vivy\Type;

trait ContextTrait
{
    public mixed $value;

    public array $errors;

    public ?ContextInterface $rootContext;

    public ?ContextInterface $fatherContext;

    private bool $isRootContext;

    protected TypeInterface $type;

    public array $args;

    public $fields;

    public $extra;

    public $index;

    public ?MiddlewareInterface $middleware;

    private function init(?ContextInterface $cloneFrom = null, ?ContextInterface $fatherContext = null): void
    {
        // TODO - if instance of GroupContext/ArrayContext/OrContext

        if ($cloneFrom instanceof Context) {
            $this->value = $cloneFrom->value;
            $this->errors = $cloneFrom->errors;
            $this->args = $cloneFrom->args();
            $this->rootContext = $cloneFrom->rootContext;
            $this->isRootContext = $cloneFrom->isRootContext();
        } else {
            $this->value = Undefined::instance();
            $this->errors = [];
            $this->args = [];
            $this->rootContext = null;
            $this->isRootContext = false;
        }

        if ($fatherContext instanceof \Kedniko\Vivy\Context) {
            $this->fatherContext = $fatherContext;
        }

        // if ($childrenContext) {
        // 	$this->childrenContext = $childrenContext;
        // }
    }

    /**
     * Get the value of fatherContext
     *
     * @return ContextInterface
     */
    public function fatherContext()
    {
        return $this->fatherContext;
    }

    /**
     * Set the value of fatherContext
     *
     * @return self
     */
    public function setFatherContext($fatherContext)
    {
        $this->fatherContext = $fatherContext;

        return $this;
    }

    /**
     * Get the value of rootContext
     *
     * @return ContextInterface
     */
    public function rootContext()
    {
        return $this->rootContext;
    }

    /**
     * Set the value of rootContext
     *
     * @return self
     */
    public function setRootContext(ContextInterface|Undefined $rootContext)
    {
        $this->rootContext = $rootContext;

        return $this;
    }

    /**
     * Set the value of errors
     *
     * @return self
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;

        return $this;
    }

    /**
     * Get the value of args
     */
    public function args()
    {
        return $this->args;
    }

    public function isArrayContext()
    {
        return $this instanceof ArrayContext;
    }

    public function isGroupContext()
    {
        return $this instanceof GroupContext;
    }

    /**
     * Set the value of args
     *
     * @param  array  $args
     * @return self
     */
    public function setArgs($args)
    {
        $this->args = $args;

        return $this;
    }

    /**
     * Get the value of isRootContext
     */
    public function isRootContext()
    {
        return $this->isRootContext;
    }

    /**
     * Set the value of isRootContext
     *
     * @return self
     */
    public function setIsRootContext($isRootContext)
    {
        $this->isRootContext = $isRootContext;

        return $this;
    }

    public function issetValue()
    {
        return ! ($this->value instanceof Undefined);
    }

    /**
     * Unset the value of issetValue
     */
    public function unsetValue()
    {
        $this->value = Undefined::instance();

        return $this;
    }

    public function isValid()
    {
        return ! $this->errors;
    }

    public function getFieldContext(string $fieldname): ?ContextInterface
    {
        return $this->fields[$fieldname] ?? null;
    }

    // /**
    //  * @param mixed $value
    //  */
    // public function setValue($value)
    // {
    // 	$this->value = $value;
    // 	return $this;
    // }

    public function getField(): Type
    {
        $type = new Type();

        // share setup
        $type->setSetup($this->type->getSetup());

        return $type;
    }

    public function setField($type): self
    {
        $this->type = $type;

        return $this;
    }

    public function setExtra(string $index, mixed $value): self
    {
        $this->extra[$index] = $value;

        return $this;
    }

    public function getRawField(): Type
    {
        return $this->type;
    }

    public function addError($key, $errormessage): void
    {
        $this->errors[$key][] = $errormessage;
    }

    public function setMiddleware(MiddlewareInterface $middleware): self
    {
        $this->middleware = $middleware;

        return $this;
    }

    public function getMiddleware(): ?MiddlewareInterface
    {
        return $this->middleware;
    }
}
