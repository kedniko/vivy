<?php

namespace Kedniko\Vivy\Type;

use Kedniko\Vivy\V;
use Kedniko\Vivy\Type;
use Kedniko\Vivy\Core\Rule;
use Kedniko\Vivy\Core\Options;
use Kedniko\Vivy\Support\Util;
use Kedniko\Vivy\Core\OrContext;
use Kedniko\Vivy\Core\Validated;
use Kedniko\Vivy\Enum\RulesEnum;
use Kedniko\Vivy\Core\LinkedList;
use Kedniko\Vivy\Support\TypeProxy;
use Kedniko\Vivy\Contracts\TypeInterface;
use Kedniko\Vivy\Contracts\ContextInterface;

final class TypeOr extends Type
{
    /**
     * @param  TypeInterface[]  $types
     */
    public function init(array $types, bool $isNot = false, Options $options = null)
    {
        foreach ($types as $key => $type) {

            if (!($type instanceof TypeInterface)) {
                if (is_array($type)) {
                    $types[$key] = V::group($type);
                }
            }
        }

        $rule = $this->getOrRule($types, $isNot, $options->getErrorMessage());
        $this->addRule($rule, $options);
        // $this->universes = $types;

        // $this->_extra['hasUndefined'] = false;
        foreach ($types as $type) {

            assert($type instanceof TypeInterface);
            $canBeNull = (new TypeProxy($type))->canBeNull();
            $canBeEmptyString = (new TypeProxy($type))->canBeEmptyString();
            if ($canBeNull) {
                $this->state->_extra['childCanBeNull'] = true;
            }
            if ($canBeEmptyString) {
                $this->state->_extra['childCanBeEmptyString'] = true;
            }

            // if (isset($type->state->_extra['startsWithUndefined']) && $type->state->_extra['startsWithUndefined']) {
            // 	$this->_extra['hasUndefined'] = true;
            // }
        }

        return $this;
    }

    /**
     * @param  TypeInterface[]  $types
     * @param  bool  $isNot - true = all rule false. false = any rule true
     */
    private function getOrRule(array $types, bool $isNot, mixed $errormessage = null): Rule
    {
        $ruleID = RulesEnum::ID_OR->value;
        $types = new LinkedList($types);
        $ruleFn = function (ContextInterface $c) use (&$types, $isNot): bool|Validated {
            $isValid = false;
            $types->rewind();
            $index = -1;

            $oc = OrContext::new(
                [],
                $c,
                $c,
            );

            while ($types->hasNext()) {
                $index++;
                $type = $types->getNext();
                assert($type instanceof TypeInterface);
                $type->_extra = ['isInsideOr' => true];

                $clonedValue = Util::clone($c->value);

                $validated = $type->validate($c->value, $oc);
                $errors = $type->_extra['or_errors'] ?? [];

                if ($errors !== []) {
                    $c->value = $clonedValue;
                    $oc->childErrors[$index] = $errors;
                } else {
                    if ($isNot) {
                        return false;
                    }
                    $c->value = $validated->value();
                    $c->errors = [];
                    $isValid = true;
                    break;
                }
            }
            $types->rewind();

            if ($isValid) {
                $this->state->_extra['or_errors'] = [];
                $c->extra['or_errors'] = [];
            } else {
                $this->state->_extra['or_errors'] = $oc->childErrors;
                $c->extra['or_errors'] = $oc->childErrors;
                // $middleware = $this->state->getMiddlewares()->getCurrent();
                // $oc = new OrContext($all_errors);
                // $oc->fatherContext = $c;
                // $c->errors = Helpers::getErrors($middleware, $this->fieldProxy, $oc);
            }

            return new Validated($c->value, $c->errors);
        };

        if ($isNot) {
            $errormessage = $errormessage ?: 'Errore: almeno un validatore ha avuto successo'; // TODO
        } else {
            $errormessage = $errormessage ?: 'Nessun validatore ha avuto successo'; // TODO
        }

        return new Rule($ruleID, $ruleFn, $errormessage);
    }
}
