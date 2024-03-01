<?php

declare(strict_types=1);

namespace Kedniko\Vivy\Traits;

use Kedniko\Vivy\Serializer;

trait Serializable
{

  public function serialize()
  {
    return (new Serializer)->encode($this);
  }

  public function unserialize($data)
  {
    return (new Serializer)->decode($data);
  }
}
