<?php

namespace Kedniko\Vivy\Plugins\StandardLibrary;

use Kedniko\Vivy\Concerns\Typeable;
use Kedniko\Vivy\Contracts\TypeContract;

final class TypeAny extends Type implements TypeContract
{
    use Typeable;
}
