<?php

namespace Kedniko\Vivy\Core;

final class Node
{
    public Node|null $next;

    public Node|null $prev;

    public function __construct(public $data)
    {
    }
}
