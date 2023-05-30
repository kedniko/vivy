<?php

namespace Kedniko\Vivy\Core;

use Kedniko\Vivy\Context;
use Kedniko\Vivy\Plugins\StandardLibrary\TypeOr;
use Kedniko\Vivy\Rules;
use Kedniko\Vivy\TypesProxy\TypeProxy;

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
     * @param  Context  $c Context passed to the user if the default value is a function
     */
    public static function tryToGetDefault($ruleID, TypeProxy $typeProxy, Context $c)
    {
        $newDefault = Undefined::instance();
        if ($typeProxy->hasDefaultValue($ruleID)) {
            $newDefault = Helpers::valueOrFunction($typeProxy->getDefaultValue($ruleID), $c);
        // if ($c instanceof GroupContext) {
        // 	$value = $c->value;
        // 	$value[$c->fieldname()] = $defaultValue;
        // 	// $c->setValue($value);
        // 	$newDefault = $value;
        // } else {
        // 	// $c->setValue($defaultValue);
        // 	$newDefault = $defaultValue;
        // }
        } elseif ($typeProxy->hasDefaultValueAny()) {
            $newDefault = Helpers::valueOrFunction($typeProxy->getDefaultValueAny(), $c);
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

    public static function getErrors(Rule $middleware, TypeProxy $typeProxy, Context $c, $errors = null)
    {
        $errors = $errors ?? $c->errors;
        $ruleID = $middleware->getID();

        if ($typeProxy->type instanceof TypeOr) {
            $c->childrenErrors = $typeProxy->getChildrenErrors();
        }

        // get error message and key

        $idEmptyRules = [Rules::ID_REQUIRED, Rules::ID_NOT_NULL, Rules::ID_NOT_EMPTY_STRING];

        if ($typeProxy->hasCustomErrorMessage($ruleID)) {
            $errormessage = $typeProxy->getCustomErrorMessage($ruleID);
            $errorKey = $ruleID;
        } elseif ($typeProxy->hasErrorMessageEmpty() && in_array($ruleID, $idEmptyRules, true)) {
            $errormessage = $typeProxy->getErrorMessageEmpty();
            $errorKey = 'error';
        } elseif ($typeProxy->hasErrorMessageAny()) {
            $errormessage = $typeProxy->getErrorMessageAny();
            $errorKey = 'error';
        } else {
            $errormessage = $middleware->getErrorMessage();
            $errorKey = $ruleID;
        }

        $errormessage = Helpers::valueOrFunction($errormessage, $c);

        $isInvisibleKey = in_array($errorKey, Rules::getInvisibleKeys(), true);

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
     * @param  mixed  $arg
     */
    public static function valueOrFunction($value, $arg)
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
            } elseif ($message_or_var !== null && strpos($message_or_var, '$') === 0) {
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

        if (is_array($value)) {
            if (count($value) === 2 && (is_string($value[0]) || is_object($value[0]))) {
                if (is_string($value[1]) && ! empty($value[1])) {
                    $class = $value[0];
                    $method = $value[1];
                } elseif (is_string($value[0])) {
                    $value = $value[0];
                } else {
                }
            }
        }

        if (! $class && ! $method && is_string($value)) {
            if (strpos($value, '@') !== false) {
                $parts = explode('@', $value);
                $class = $parts[0];
                $method = $parts[1];
            } elseif (strpos($value, '::') !== false) {
                $parts = explode('::', $value);
                $class = $parts[0];
                $method = $parts[1];
            } elseif (strpos($value, ',') !== false) {
                $parts = explode(',', $value);
                $class = $parts[0];
                $method = $parts[1];
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
}
