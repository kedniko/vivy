<?php

namespace Kedniko\Vivy\Plugins\StandardLibrary;

use Kedniko\Vivy\Context;
use Kedniko\Vivy\Core\GroupContext;
use Kedniko\Vivy\Core\Helpers;
use Kedniko\Vivy\Core\LinkedList;
use Kedniko\Vivy\Core\Options;
use Kedniko\Vivy\Core\Rule;
use Kedniko\Vivy\Core\Undefined;
use Kedniko\Vivy\Core\Validated;
use Kedniko\Vivy\Exceptions\VivyException;
use Kedniko\Vivy\Messages\RuleMessage;
use Kedniko\Vivy\Rules;
use Kedniko\Vivy\Types\Type;
use Kedniko\Vivy\TypesProxy\TypeProxy;
use Kedniko\Vivy\V;

class TypeGroup extends TypeCompound
{
    /**
     * @param  array|callable  $setup function(GroupBuilder $type)
     */
    public function init($setup = null)
    {
        if (! $setup) {
            return;
        }

        if (is_array($setup)) {
            $types = $this->getFieldsFromAssociativeArraySetup($setup);
            $this->state->setFields($types);
        } elseif (is_callable($setup)) {
            $this->state->setupFn = $setup;
        }

        return $this;
    }

    // public function group($setup = null, bool $stopOnFieldFailure = false, Options $options = null)
    // {
    // 	$options = Options::build($options, func_get_args());
    // 	Helpers::assertTrueOrFail(is_bool($stopOnFieldFailure), '$stopOnFieldFailure is not a bool');
    // 	$this->addRule($this->groupRule($stopOnFieldFailure, $options->getErrorMessage()), $options);

    // 	return $this;
    // }

    /**
     * @param  LinkedList|callable  $types
     * @param  mixed  $errormessage
     */
    public function getGroupRule(bool $stopOnFieldFailure, $errormessage)
    {
        $ruleID = Rules::ID_GROUP;
        $ruleFn = function (Context $c) use ($stopOnFieldFailure) {
            $typeFields = $this->getFieldsFromState($c);

            $continueLoopFields = true;

            $typeFields->rewind();
            while ($typeFields->hasNext() && $continueLoopFields) {
                /** @var Type */
                $type = $typeFields->getNext();
                $fieldname = $type->getName();

                $typeProxy = (new TypeProxy($type));
                $middlewares = $type->state->getMiddlewares();
                $middlewares->rewind();
                $hasNextMiddleare = $middlewares->hasNext();
                if ($hasNextMiddleare) {
                    $nextMiddleware = $middlewares->getNext();
                    $nextIsUndefined = $nextMiddleware instanceof Rule && $nextMiddleware->getID() === Rules::ID_UNDEFINED;
                } else {
                    $nextIsUndefined = false;
                }

                $gc = GroupContext::build($fieldname, $c, $c->value[$fieldname] ?? Undefined::instance(), $c);

                $isRequired = $typeProxy->isRequired($gc) && ! $nextIsUndefined;

                $isInOr = $type instanceof TypeOr;

                if (! is_array($c->value)) {
                    throw new \Exception('$c->value in not an array', 1);
                }

                if (! array_key_exists($fieldname, $c->value) && ! $isInOr && $isRequired) {
                    $rule = $type->state->getRequiredRule() ?: Rules::required();

                    $newDefault = Helpers::tryToGetDefault($rule->getID(), $typeProxy, $gc);
                    if (Helpers::isNotUndefined($newDefault)) {
                        $c->value[$fieldname] = $newDefault;
                    } else {
                        $errors = Helpers::getErrors($rule, $typeProxy, $c, []);
                        $c->errors[$fieldname] = $errors; // TODO check if this is correct
                        $c->value[$fieldname] = Undefined::instance();
                    }
                } else {
                    $typeValue = array_key_exists($fieldname, $c->value) ? $c->value[$fieldname] : Undefined::instance();
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
                if ($type->state->getOnce()) {
                    $typeFields->removeCurrent();
                }
            }
            $typeFields->rewind();

            $validated = new Validated($c->value, $c->errors);

            return $validated;
        };

        if ($errormessage === null) {
            $errormessage = function (Context $c) {
                return $c->errors;
            };
        }

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    /**
     * @return LinkedList
     */
    private function getFieldsFromState(Context $c)
    {
        if (isset($this->state->setupFn) && is_callable($this->state->setupFn)) {
            $fn = $this->state->setupFn;
            $arraySetup = $fn($c);
            $types = $this->getFieldsFromAssociativeArraySetup($arraySetup);
        } else {
            $types = $this->state->getFields();
        }

        return $types;
    }

    private function buildFieldFromString($setup)
    {
        $type = $this->getNewUnkownField();
        foreach (explode('|', $setup) as $rule) {
            $this->applyRuleOnField($rule, $type);
        }

        return $type;
    }

    private function buildFieldFromCallable($setup)
    {
        $type = $this->getNewUnkownEmptyField();
        $type->state->setupFn = $setup;

        return $type;
    }

    private function buildFieldFromArray($setup)
    {
        $type = V::group($setup);

        return $type;
    }

    private function getFieldsFromAssociativeArraySetup($setupArray)
    {
        $types = new LinkedList();

        foreach ($setupArray as $fieldname => $type) {
            if (is_string($type)) {
                $type = $this->buildFieldFromString($type);
            } elseif (is_callable($type)) {
                $type = $this->buildFieldFromCallable($type);
            } elseif (is_array($type)) {
                $type = $this->buildFieldFromArray($type);
            } elseif ($type instanceof Type) {
                // do nothing
            } else {
                throw new VivyException('Unknown setup type: '.gettype($type));
            }

            if (! $type->state->issetRequired()) {
                $type->state->setRequired(true, Rules::required());
            }
            (new TypeProxy($type))->setName($fieldname);
            $types->append($type);
        }

        return $types;
    }

    // /**
    //  * @param mixed $rule
    //  * @param GroupCustom $g
    //  */
    private function applyRuleOnField($rule, $type)
    {
        if ($rule === 'optional') {
            $type->optional();
        }
        if ($rule === 'string') {
            $type->string();
        }
        if ($rule === 'email') {
            $type->email();
        }

        if (count($parts = explode(':', $rule)) === 2) {
            if ($parts[0] === 'minlen') {
                $type->string()->minLength($parts[1]);
            }
            if ($parts[0] === 'maxlen') {
                $type->string()->maxLength($parts[1]);
            }
        }
    }

    /**
     * @param  mixed  $name
     * @return TypeAny
     */
    private function getNewUnkownField(Options $options = null)
    {
        $options = Helpers::getOptions($options);
        $errormessage = $options->getErrorMessage();

        $type = new TypeAny();
        $type->required($options);
        $type->addRule(Rules::notNull($errormessage ?: RuleMessage::getErrorMessage('default.notNull')), $options);
        $type->addRule(Rules::notEmptyString($errormessage ?: RuleMessage::getErrorMessage('default.notEmptyString')), $options);

        return $type;
    }

    private function getNewUnkownEmptyField()
    {
        $type = new TypeAny();

        return $type;
    }

    // public function addField($name, BasicField $type, Options $options = null)
    // {
    // 	$options = Helpers::getOptions($options);
    // 	$errormessage = $options->getErrormessage();

    // 	$type = new GroupString($name);
    // 	$this->fields->append(new Node($type));
    // }

    /**
     * @param  string  $fieldname
     * @param  Type  $type
     */
    public function addField($fieldname, $type)
    {
        /** @var LinkedList $types */
        $types = $this->state->getFields();
        (new TypeProxy($type))->setName($fieldname);
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
}
