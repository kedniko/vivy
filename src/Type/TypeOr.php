<?php

namespace Kedniko\Vivy\Type;

use Kedniko\Vivy\Contracts\ContextInterface;
use Kedniko\Vivy\Contracts\TypeInterface;
use Kedniko\Vivy\Core\LinkedList;
use Kedniko\Vivy\Core\Options;
use Kedniko\Vivy\Core\OrContext;
use Kedniko\Vivy\Core\Rule;
use Kedniko\Vivy\Core\Undefined;
use Kedniko\Vivy\Core\Validated;
use Kedniko\Vivy\Enum\RulesEnum;
use Kedniko\Vivy\Support\Util;
use Kedniko\Vivy\Type;
use Kedniko\Vivy\V;

final class TypeOr extends Type
{
    /**
     * @param  TypeInterface[]  $types
     */
    public function init(array $types, bool $isNot = false, ?Options $options = null)
    {
        foreach ($types as $key => $type) {

            if (! ($type instanceof TypeInterface)) {
                if (is_array($type)) {
                    $types[$key] = V::group($type);
                }
            }
        }

        $rule = $this->getOrRule($types, $isNot, $options->getErrorMessage());
        $this->addRule($rule, $options);
     
        return $this;
    }

    /**
     * @param  TypeInterface[]  $types
     * @param  bool  $isNot  - true = all rule false. false = any rule true
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

            // TODO clone for performance and data integrity reasons - check if it's necessary
            // users should not change value inside a rule - it's a bad practice - elaborate more...
            // IDEA: lazy-clone after ->setValue() call inside rules
            $clonedValue = Util::clone($c->value);

            // $clonedValue = $c->value;

            // $clonedValue = Undefined::instance();
            // $cProxy = new Proxy($c, [
            //     'set' => function (ContextInterface $target, string $property, mixed $value) use ($c, &$clonedValue) {
            //         if ($property === 'value') {
            //             if ($clonedValue instanceof Undefined) {
            //                 // save the original value before it's changed
            //                 $clonedValue = Util::clone($c->value);
            //             }
            //             $target->{$property} = $value;
            //         } else {
            //             $target->{$property} = $value;
            //         }
            //     },
            // ]);

            // loop through all types

            while ($types->hasNext()) {
                $index++;
                $type = $types->getNext();
                assert($type instanceof TypeInterface);
                $type->_extra = ['isInsideOr' => true];

                $validated = $type->validate($c->value, $oc);
                $errors = $type->_extra['or_errors'] ?? [];

                $hasErrors = $errors !== [];

                if ($hasErrors) {
                    if (! ($clonedValue instanceof Undefined)) {
                        $c->value = $clonedValue; // restore original value
                    }
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
                $this->getSetup()->_extra['or_errors'] = [];
                $c->extra['or_errors'] = [];
            } else {
                $this->getSetup()->_extra['or_errors'] = $oc->childErrors;
                $c->extra['or_errors'] = $oc->childErrors;
                // $middleware = $this->getSetup()->getMiddlewares()->getCurrent();
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
