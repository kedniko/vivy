<?php

namespace Kedniko\Vivy\Core;

use Kedniko\Vivy\Concerns\ContextTrait;
use Kedniko\Vivy\Contracts\ContextInterface;
use Kedniko\Vivy\Contracts\TypeInterface;
use Kedniko\VivyPluginStandard\Rules;
use Kedniko\Vivy\Support\TypeProxy;

final class GroupContext implements ContextInterface
{
    use ContextTrait;

    private $fieldname;

    public function __construct($fieldname, ContextInterface $cloneFrom = null, ContextInterface $fatherContext = null)
    {
        $this->init($cloneFrom, $fatherContext);
        $this->fieldname = $fieldname ?? Undefined::instance();
    }

    public static function build($fieldname, $fatherContext, $value, ContextInterface $cloneFrom = null): GroupContext
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
     * @param  TypeInterface  $type
     * @param  mixed  $permanent Mutate the setup permanently
     */
    public function appendField(mixed $fieldname, $type, mixed $permanent = false)
    {
        // setup field

        $typeProxy = new TypeProxy($type);
        $typeProxy->setName($fieldname);
        if (!$permanent) {
            $type->once();
        }
        if (!$typeProxy->getState()->issetRequired()) {
            $typeProxy->getState()->setRequired(true, Rules::required());
        }

        // add field

        $types = $this->getFields();
        assert($types instanceof LinkedList);
        $types->append($type);

        return $this;
    }

    public function appendFieldAfterCurrent($fieldname, $type, $permanent = false)
    {
        // setup field

        $typeProxy = new TypeProxy($type);
        $typeProxy->setName($fieldname);
        if (!$permanent) {
            $type->once();
        }
        if (!$typeProxy->getState()->issetRequired()) {
            $typeProxy->getState()->setRequired(true, Rules::required());
        }

        // add field

        $types = $this->getFields();
        assert($types instanceof LinkedList);
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
