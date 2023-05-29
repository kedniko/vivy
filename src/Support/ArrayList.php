<?php

namespace Kedniko\Vivy\Support;

class ArrayList
{
    private $items = [];

    public function add($obj, $key = null)
    {
        $this->items[] = $obj;
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
