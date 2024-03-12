<?php

namespace Kedniko\Vivy\Core;

final class Node
{
    public ?Node $next;

    public ?Node $prev;

    public function __construct(public $data)
    {
    }
}
