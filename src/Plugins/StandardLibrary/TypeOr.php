<?php

namespace Kedniko\Vivy\Plugins\StandardLibrary;

use Kedniko\Vivy\Contracts\Context;
use Kedniko\Vivy\Core\Helpers;
use Kedniko\Vivy\Core\LinkedList;
use Kedniko\Vivy\Core\Options;
use Kedniko\Vivy\Core\OrContext;
use Kedniko\Vivy\Core\Rule;
use Kedniko\Vivy\Core\Validated;
use Kedniko\Vivy\Rules;
use Kedniko\Vivy\Support\Util;
use Kedniko\Vivy\TypesProxy\TypeProxy;

final class TypeOr extends Type
{
    /**
     * @param  Type[]  $types
     */
    public function init(array $types, $isNot = false, Options $options = null)
    {
        $this->addRule($this->getOrRule($types, $isNot, $options->getErrorMessage()), $options);
        // $this->universes = $types;

        // $this->_extra['hasUndefined'] = false;
        foreach ($types as $type) {
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
     * @param  Type[]  $types
     * @param  bool  $isNot - true = all rule false. false = any rule true
     */
    private function getOrRule(array $types, $isNot, mixed $errormessage = null): Rule
    {
        $ruleID = Rules::ID_OR;
        $types = new LinkedList($types);
        $ruleFn = function (Context $c) use (&$types, $isNot): bool|\Kedniko\Vivy\Core\Validated {
            $all_errors = [];

            $isValid = false;
            $types->rewind();
            $index = -1;
            while ($types->hasNext()) {
                $index++;
                $type = $types->getNext();
                $type->_extra = ['isInsideOr' => true];

                $clonedValue = Util::clone($c->value);
                $validated = $type->validate($c->value, $c);
                $errors = $type->_extra['or_errors'] ?? [];

                if ($errors !== []) {
                    $c->value = $clonedValue;
                    $all_errors[$index] = $errors;
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

            if (!$isValid) {
                $this->state->_extra['or_errors'] = $all_errors;
                // $c->errors = [1];
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
