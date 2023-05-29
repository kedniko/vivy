<?php

namespace Kedniko\Vivy\Core;

class Node
{
    public $data;

    /** @var Node|null */
    public $next;

    /** @var Node|null */
    public $prev;

    public function __construct($data)
    {
        $this->data = $data;
        $this->next = null;
        $this->prev = null;
    }
}
