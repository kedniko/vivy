<?php

namespace Kedniko\Vivy\Type;

use Kedniko\Vivy\Contracts\ContextInterface;
use Kedniko\Vivy\Contracts\TypeInterface;
use Kedniko\Vivy\Core\GroupContext;
use Kedniko\Vivy\Core\Helpers;
use Kedniko\Vivy\Core\LinkedList;
use Kedniko\Vivy\Core\Rule;
use Kedniko\Vivy\Core\Undefined;
use Kedniko\Vivy\Core\Validated;
use Kedniko\Vivy\Enum\RulesEnum;
use Kedniko\Vivy\Exceptions\VivyException;
use Kedniko\Vivy\Rules;
use Kedniko\Vivy\V;

final class TypeGroup extends TypeCompound
{
    public function init(array|callable|null $setup = null)
    {
        if (! $setup) {
            return $this;
        }

        if (is_array($setup)) {
            $types = $this->getTypeFromArray($setup);
            $this->getSetup()->setFields($types);
        } elseif (is_callable($setup)) {
            $this->getSetup()->setupFn = $setup;
        }

        return $this;
    }

    public function getGroupRule(bool $stopOnFieldFailure, mixed $errormessage): Rule
    {
        $ruleID = RulesEnum::ID_GROUP->value;
        $ruleFn = function (ContextInterface $c) use ($stopOnFieldFailure): Validated {
            $typeFields = $this->getFieldsFromState($c);

            $continueLoopFields = true;

            $typeFields->rewind();
            while ($typeFields->hasNext() && $continueLoopFields) {
                $type = $typeFields->getNext();
                $fieldname = $type->getName();

                assert($type instanceof TypeInterface);

                $middlewares = $type->getSetup()->getMiddlewaresAndRewind();
                $hasNextMiddleare = $middlewares->hasNext();
                if ($hasNextMiddleare) {
                    $nextMiddleware = $middlewares->getNext();
                    $nextRuleIsUndefined = $nextMiddleware instanceof Rule && $nextMiddleware->getID() === RulesEnum::ID_UNDEFINED->value;
                } else {
                    $nextRuleIsUndefined = false;
                }

                $gc = GroupContext::build(
                    $fieldname,
                    $c,
                    $c->value[$fieldname] ?? Undefined::instance(),
                    $c
                );

                $isRequired = $type->getSetup()->isRequired($gc);

                $isInOr = $type instanceof TypeOr;

                if (! is_array($c->value)) {
                    throw new \Exception('$c->value in not an array', 1);
                }

                $caseRuleRequiredFailed = ! array_key_exists($fieldname, $c->value) &&
                    $isRequired &&
                    ! $isInOr &&
                    ! $nextRuleIsUndefined;

                if ($caseRuleRequiredFailed) {

                    // CASE: required rule failed

                    $rule = $type->getSetup()->getRequiredRule() ?: Rules::required();

                    $newDefault = Helpers::tryToGetDefault($rule->getID(), $type, $gc);
                    if (Helpers::isNotUndefined($newDefault)) {
                        $c->value[$fieldname] = $newDefault;
                    } else {
                        $errors = Helpers::getErrors($rule, $type, $c, []);
                        $c->errors[$fieldname] = $errors; // TODO check if this is correct
                        $c->value[$fieldname] = Undefined::instance();
                    }
                } else {
                    $typeValue = array_key_exists($fieldname, $c->value)
                        ? $c->value[$fieldname]
                        : Undefined::instance();

                    $validated = $type->validate($typeValue, $c);

                    // TODO check if this is correct
                    if ($validated->fails() && $typeValue instanceof Undefined) {
                        continue;
                    }

                    // apply middlewares
                    $typeValue = $validated->value();
                    $c->value[$fieldname] = $typeValue;

                    // get errors
                    if ($validated->fails()) {
                        $c->errors[$fieldname] = Helpers::issetOrDefault($c->errors[$fieldname], []);
                        $a_oldErrors = $c->errors[$fieldname];
                        $a_newErrors = $validated->errors();
                        $a_merged = array_replace_recursive($a_oldErrors, $a_newErrors);
                        $c->errors[$fieldname] = $a_merged;

                        if ($stopOnFieldFailure || $type->getStopOnFailure()) {
                            $continueLoopFields = false;
                        }
                    }
                }

                $c->fields[$fieldname] = $gc;

                $isValueUndefined = $c->value[$fieldname] instanceof Undefined;
                if ($isValueUndefined) {
                    unset($c->value[$fieldname]);
                }

                // remove temporary field
                if ($type->getSetup()->getOnce()) {
                    $typeFields->removeCurrent();
                }
            }
            $typeFields->rewind();

            return new Validated($c->value, $c->errors);
        };

        if ($errormessage === null) {
            $errormessage = fn (ContextInterface $c) => $c->errors;
        }

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    private function getFieldsFromState(ContextInterface $c): \Kedniko\Vivy\Core\LinkedList
    {
        if ($this->getSetup()->setupFn !== null && is_callable($this->getSetup()->setupFn)) {
            $fn = $this->getSetup()->setupFn;
            $arraySetup = $fn($c);

            assert(is_array($arraySetup));

            return $this->getTypeFromArray($arraySetup);
        }

        return $this->getSetup()->getFields();
    }

    // public function addField($name, BasicField $type, Options $options = null)
    // {
    // 	$options = Helpers::getOptions($options);
    // 	$errormessage = $options->getErrormessage();

    // 	$type = new GroupString($name);
    // 	$this->fields->append(new Node($type));
    // }

    public function addField(string $fieldname, TypeInterface $type)
    {
        /** @var LinkedList[TypeInterface] $types */
        $types = $this->getSetup()->getFields();
        $type->getSetup()->setName($fieldname);
        $types->append($type);

        return $this;
    }

    public function addFields($types)
    {
        foreach ($types as $fieldname => $type) {
            $this->addField($fieldname, $type);
        }

        return $this;
    }

    private function getTypeFromArray(array $setupArray): LinkedList
    {
        $types = new LinkedList();

        foreach ($setupArray as $fieldname => $type) {
            if (is_string($type)) {
                $type = V::is($type, true);
            } elseif (is_callable($type)) {
                $setupFn = $type;
                $type = TypeAny::new();
                $type->getSetup()->setupFn = $setupFn;
            } elseif (is_array($type)) {
                $type = V::group($type);
            } elseif ($type instanceof TypeInterface) {
                // do nothing
            } else {
                throw new VivyException('Unknown setup type: '.gettype($type));
            }

            if (! $type->getSetup()->issetRequired()) {
                $type->getSetup()->setRequired(true, Rules::required());
            }
            $type->getSetup()->setName($fieldname);
            $types->append($type);
        }

        return $types;
    }
}
