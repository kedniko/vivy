<?php

namespace Kedniko\Vivy\Support;

final class Map
{
    private array $items = [];

    public function add($obj, $key): void
    {
        $this->items[$key] = $obj;
    }

    public function remove($key): void
    {
        if (isset($this->items[$key])) {
            unset($this->items[$key]);
        }
    }

    public function get($key)
    {
        if (isset($this->items[$key])) {
            return $this->items[$key];
        }
    }

    public function toArray(): array
    {
        return $this->items;
    }
}
