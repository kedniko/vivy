<?php

namespace Kedniko\Vivy\Support;

final class Registrar
{
    public function __construct(
        public ?string $id = null,
        public array|string|null $for = null,
        public array|string|null $callback = null,
        public ?string $return = null,
    ) {
    }

    public static function make(string $id): static
    {
        return new self(
            id: $id
        );
    }

    public function for(array|string $classes)
    {
        $this->for = $classes;

        return $this;
    }

    public function callback(array|string $callback)
    {
        $this->callback = $callback;

        return $this;
    }

    public function return(string $callback)
    {
        $this->return = $callback;

        return $this;
    }

    public function get(): array
    {
        return [
            $this->for,
            $this->id,
            $this->callback,
            $this->return,
        ];
    }
}
