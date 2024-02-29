<?php

namespace Kedniko\Vivy\Core;

final class ErrorBag
{
    private array $errors = [];

    public function add(string $field, string $message): void
    {
        $this->errors[$field] = $message;
    }

    public function has(string $field): bool
    {
        return isset($this->errors[$field]);
    }

    public function get(string $field): string
    {
        return $this->errors[$field];
    }

    public function all(): array
    {
        return $this->errors;
    }
}
