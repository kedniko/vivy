<?php

namespace Kedniko\Vivy\Contracts;

interface ContextInterface
{
    public function fatherContext();

    public function setFatherContext($fatherContext);

    public function rootContext();

    public function setRootContext(ContextInterface|\Kedniko\Vivy\Core\Undefined $rootContext);

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

    public function getField(): TypeInterface;

    public function setMiddleware(MiddlewareInterface $middleware): self;

    public function getMiddleware(): MiddlewareInterface|null;
}
