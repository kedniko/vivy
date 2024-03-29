<?php

namespace Kedniko\Vivy\Core;

use Kedniko\Vivy\Concerns\MiddlewareTrait;
use Kedniko\Vivy\Contracts\MiddlewareInterface;

final class Middleware implements MiddlewareInterface
{
    use MiddlewareTrait;
}
