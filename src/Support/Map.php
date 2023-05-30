<?php

namespace Kedniko\Vivy\Support;

final class Map
{
    private $items = [];

    public function add($obj, $key)
    {
        $this->items[$key] = $obj;
    }

    public function remove($key)
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
