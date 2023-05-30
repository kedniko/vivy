<?php

namespace Kedniko\Vivy\Support;

final class ArrayList
{
    private array $items = [];

    public function add($obj, $key = null): void
    {
        $this->items[] = $obj;
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

    public function toArray()
    {
        return $this->items;
    }
}
