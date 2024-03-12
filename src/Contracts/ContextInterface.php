<?php

namespace Kedniko\Vivy\Contracts;

use Kedniko\Vivy\Core\Undefined;
use Kedniko\Vivy\Type;

interface ContextInterface
{
    public function fatherContext();

    public function setFatherContext($fatherContext);

    public function rootContext();

    public function setRootContext(ContextInterface|Undefined $rootContext);

    public function setErrors($errors);

    public function args();

    public function isArrayContext();

    public function isGroupContext();

    public function setArgs($args);

    public function isRootContext();

    public function setIsRootContext($isRootContext);

    public function issetValue();

    public function unsetValue();

    public function isValid();

    public function getFieldContext(string $fieldname): ?ContextInterface;

    public function getField(): Type;

    public function setMiddleware(MiddlewareInterface $middleware): self;

    public function getMiddleware(): ?MiddlewareInterface;
}
