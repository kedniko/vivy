<?php

namespace Kedniko\Vivy\Core;

use Kedniko\Vivy\Context;
use Kedniko\Vivy\Rules;
use Kedniko\Vivy\Types\Type;
use Kedniko\Vivy\TypesProxy\TypeProxy;

final class GroupContext extends Context
{
    private $fieldname;

    public function __construct($fieldname, Context $cloneFrom = null)
    {
        parent::__construct($cloneFrom);
        $this->fieldname = $fieldname ?? Undefined::instance();
    }

    public static function build($fieldname, $fatherContext, $value, Context $cloneFrom = null): GroupContext
    {
        $gc = new GroupContext($fieldname, $cloneFrom);
        $gc->fatherContext = $fatherContext;
        $gc->value = $value;

        return $gc;
    }

    /**
     * Get the value of fieldname
     */
    public function fieldname()
    {
        return $this->fieldname;
    }

    /**
     * Appends field at the end
     *
     * @param  Type  $type
     * @param  mixed  $permanent Mutate the setup permanently
     */
    public function appendField(mixed $fieldname, $type, mixed $permanent = false)
    {
        // setup field

        $typeProxy = new TypeProxy($type);
        $typeProxy->setName($fieldname);
        if (! $permanent) {
            $type->once();
        }
        if (! $typeProxy->getState()->issetRequired()) {
            $typeProxy->getState()->setRequired(true, Rules::required());
        }

        // add field

        /** @var LinkedList $types */
        $types = $this->getFields();
        $types->append($type);

        return $this;
    }

    public function appendFieldAfterCurrent($fieldname, $type, $permanent = false)
    {
        // setup field

        $typeProxy = new TypeProxy($type);
        $typeProxy->setName($fieldname);
        if (! $permanent) {
            $type->once();
        }
        if (! $typeProxy->getState()->issetRequired()) {
            $typeProxy->getState()->setRequired(true, Rules::required());
        }

        // add field

        /** @var LinkedList $types */
        $types = $this->getFields();
        $types->appendAfterCurrent($type);

        return $this;
    }

    /**
     * @return LinkedList
     */
    public function getFields()
    {
        return (new TypeProxy($this->fatherContext()->type))->getState()->getFields();
    }
}
