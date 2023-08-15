<?php

namespace Kedniko\Vivy\Core;

use Kedniko\Vivy\Concerns\MiddlewareTrait;
use Kedniko\Vivy\Contracts\MiddlewareInterface;

final class Rule implements MiddlewareInterface
{
  use MiddlewareTrait;
}
