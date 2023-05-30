<?php

namespace Kedniko\Vivy\Core;

final class Node
{
    public $data;

    /** @var Node|null */
    public $next = null;

    /** @var Node|null */
    public $prev = null;

    public function __construct($data)
    {
        $this->data = $data;
    }
}
