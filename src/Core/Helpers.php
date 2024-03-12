<?php

namespace Kedniko\Vivy\Core;

use Kedniko\Vivy\Contracts\ContextInterface;
use Kedniko\Vivy\Contracts\TypeInterface;
use Kedniko\Vivy\Enum\RulesEnum;

final class Helpers
{
    public static function isNotUndefined($value)
    {
        return ! self::isUndefined($value);
    }

    public static function isUndefined($value)
    {
        return $value instanceof Undefined;
    }

    /**
     * @param  string  $ruleID
     * @param  ContextInterface  $c  Context passed to the user if the default value is a function
     */
    public static function tryToGetDefault($ruleID, TypeInterface $type, ContextInterface $c)
    {
        $newDefault = Undefined::instance();
        if (self::hasDefaultValue($type, $ruleID)) {
            $newDefault = Helpers::valueOrFunction(self::getDefaultValue($type, $ruleID), $c);
            // if ($c instanceof GroupContext) {
            // 	$value = $c->value;
            // 	$value[$c->fieldname()] = $defaultValue;
            // 	// $c->setValue($value);
            // 	$newDefault = $value;
            // } else {
            // 	// $c->setValue($defaultValue);
            // 	$newDefault = $defaultValue;
            // }
        } elseif (self::hasDefaultValueAny($type)) {
            $newDefault = Helpers::valueOrFunction(self::getDefaultValueAny($type), $c);
            // if ($c instanceof GroupContext) {
            // 	$value = $c->fatherContext()->value();
            // 	$value[$c->fieldname()] = $defaultValue;
            // 	// $c->setValue($value);
            // 	$newDefault = $value;
            // } else {
            // 	// $c->setValue($defaultValue);
            // 	$newDefault = $defaultValue;
            // }
        }

        return $newDefault;
    }

    public static function getErrors(Rule $middleware, TypeInterface $type, ContextInterface $c, $errors = null)
    {
        $errors ??= $c->errors;
        $ruleID = $middleware->getID();

        $c->setMiddleware($middleware);

        // if ($typeProxy->type instanceof TypeOr) {
        //     $c->childrenErrors = $typeProxy->getChildrenErrors();
        // }

        // get error message and key

        $idEmptyRules = [
            RulesEnum::ID_REQUIRED->value,
            RulesEnum::ID_NOT_NULL->value,
            RulesEnum::ID_NOT_EMPTY_STRING->value,
        ];

        if ($type->getSetup()->hasCustomErrorMessage($ruleID)) {
            $errormessage = $type->getSetup()->getCustomErrorMessage($ruleID);
            $errorKey = $ruleID;
        } elseif ($type->getSetup()->hasErrorMessageEmpty() && in_array($ruleID, $idEmptyRules, true)) {
            $errormessage = $type->getSetup()->getErrorMessageEmpty();
            $errorKey = 'error';
        } elseif ($type->getSetup()->hasErrorMessageAny()) {
            $errormessage = $type->getSetup()->getErrorMessageAny();
            $errorKey = 'error';
        } else {
            $errormessage = $middleware->getErrorMessage();
            $errorKey = $ruleID;
        }

        $errormessage = Helpers::valueOrFunction($errormessage, $c);

        $isInvisibleKey = in_array($errorKey, self::getInvisibleKeys(), true);

        if ($isInvisibleKey) {
            $errors = $errormessage;
        } else {
            $errors[$errorKey][] = $errormessage;
            // if ($c instanceof GroupContext) {
            // 	$errors[$c->fieldname()][$errorKey] = $errormessage;
            // } else {
            // 	$errors[$errorKey] = $errormessage;
            // }
        }

        return $errors;
    }

    public static function getOptions($options = null)
    {
        if ($options instanceof Options) {
            return $options;
        }

        return new Options();
    }

    /**
     * @param  mixed|callable  $value
     */
    public static function valueOrFunction($value, mixed $arg)
    {
        if (is_callable($value)) {
            $value = $value($arg);
        }
        if (Helpers::isUndefined($value)) {
            throw new \Exception('You cannot return Undefined');
        }

        return $value;
    }

    public static function issetOrFail(&$variable, $message_or_var = null)
    {
        if (! isset($variable)) {
            if ($message_or_var === null) {
                $message_or_var = 'Variable not set';
            } elseif ($message_or_var !== null && str_starts_with((string) $message_or_var, '$')) {
                $message_or_var = "{$message_or_var} is not set";
            }
            throw new \Exception($message_or_var, 1);
        }

        return $variable;
    }

    public static function issetOrDefault(&$variable, $defaultValue)
    {
        if (! isset($variable)) {
            return $defaultValue;
        }

        return $variable;
    }

    public static function assertTrueOrFail($bool, $message_or_var = null)
    {
        if (! $bool) {
            $default = 'Assertion failed';
            throw new \Exception("{$default}: {$message_or_var}" ?: $default, 1);
        }
    }

    public static function assertTrueOrDefault($bool, $defaultValue)
    {
        return $bool === true ? true : $defaultValue;
    }

    public static function notNullOrDefault(&$variable, $defaultValue)
    {
        if ($variable === null) {
            return $defaultValue;
        }

        return $variable;
    }

    public static function getClassAndMethod($value)
    {
        $class = null;
        $method = null;

        if (is_array($value) && (count($value) === 2 && (is_string($value[0]) || is_object($value[0])))) {
            if (is_string($value[1]) && ! empty($value[1])) {
                $class = $value[0];
                $method = $value[1];
            } elseif (is_string($value[0])) {
                $value = $value[0];
            } else {
            }
        }

        if (! $class && ! $method && is_string($value)) {
            $separators = ['::', '@', ','];
            foreach ($separators as $separator) {
                if (str_contains($value, $separator)) {
                    $parts = explode($separator, $value);
                    $class = $parts[0];
                    $method = $parts[1];
                    break;
                }
            }
        }
        if ($class === null) {
            return false;
        }
        if ($method === null) {
            return false;
        }

        if (is_string($class)) {
            $class = '\\'.ltrim(str_replace('/', '\\', ltrim($class, '\\')), '\\');
        }

        if (! method_exists($class, $method)) {
            return false;
        }

        return [$class, $method];
    }

    public static function getInvisibleKeys(): array
    {
        return [
            RulesEnum::ID_GROUP->value,
            RulesEnum::ID_EACH->value,
        ];
    }

    public static function hasDefaultValue(TypeInterface $type, $ruleID): bool
    {
        return array_key_exists($ruleID, $type->getSetup()->getDefaultValues());
    }

    public static function getDefaultValue(TypeInterface $type, $ruleID)
    {
        return self::hasDefaultValue($type, $ruleID) ? $type->getSetup()->getDefaultValues()[$ruleID] : null;
    }

    public static function hasDefaultValueAny(TypeInterface $type)
    {
        return $type->getSetup()->hasDefaultValuesAny();
    }

    public static function getDefaultValueAny(TypeInterface $type)
    {
        return $type->getSetup()->getDefaultValueAny();
    }
}
