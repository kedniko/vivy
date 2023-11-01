<?php

namespace Kedniko\Vivy\Concerns;

use Kedniko\Vivy\Plugins\StandardLibrary\Rules;
use Kedniko\Vivy\Transformer;

trait Typeable
{
    public function setValue(mixed $callback_or_value)
    {
        $callback = is_callable($callback_or_value) ? $callback_or_value : fn () => $callback_or_value;
        $transformer = new Transformer(Rules::ID_SET_VALUE, $callback);
        $type = (new \Kedniko\Vivy\Type())->from($this);
        $type->addTransformer($transformer);

        return $type;
    }
}
