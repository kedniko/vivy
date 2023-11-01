<?php

namespace Kedniko\Vivy\Support;

use Kedniko\Vivy\Core\Middleware;

final class MagicCaller
{
    public $registeredMiddlewares = [];

    public function __construct()
    {
        //
    }

    public function register(
        string $id,
        string $methodName,
        string|array $function,
        string $availableForType,
        string $returnType,
    ): void {
        $this->registeredMiddlewares[$availableForType][$methodName] = [
            'methodName' => $methodName,
            'function' => $function,
            'availableForType' => $availableForType,
            'returnType' => $returnType,
        ];
    }

    public function getId($id)
    {
        return $this->registeredMiddlewares[$id];
    }

    public function hasId($id): bool
    {
        return array_key_exists($id, $this->registeredMiddlewares);
    }

    public function addToId(string $id, Middleware $middleware): void
    {
        $this->registeredMiddlewares[$id] = $middleware;
    }

    public function toArray()
    {
        return $this->registeredMiddlewares;
    }
}
