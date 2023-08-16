<?php

namespace Kedniko\Vivy\Core;

use Kedniko\Vivy\Concerns\ContextTrait;
use Kedniko\Vivy\Contracts\Context;

final class NullContext implements Context
{
  use ContextTrait;

  public function __construct(Context $cloneFrom = null, Context $fatherContext = null)
  {
    $this->init($cloneFrom, $fatherContext);
  }
}
