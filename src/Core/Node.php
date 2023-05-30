<?php

namespace Kedniko\Vivy\Core;

final class Node
{
    /** @var Node|null */
    public $next;

    /** @var Node|null */
    public $prev;

    public function __construct(public $data)
    {
    }
}
