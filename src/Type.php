<?php

namespace Kedniko\Vivy;

use Kedniko\Vivy\Traits\Typeable;
use Kedniko\Vivy\Traits\Serializable;
use Kedniko\Vivy\Contracts\TypeInterface;

class Type implements TypeInterface, Serializable
{
    use Typeable, Serializable;
}
