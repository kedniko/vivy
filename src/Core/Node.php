<?php

namespace Kedniko\Vivy\Core;

final class Node
{
    /** @var Node|null */
    public $next = null;

    /** @var Node|null */
    public $prev = null;

    public function __construct(public $data)
    {
    }
}
