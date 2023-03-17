<?php

namespace Kedniko\Vivy;

use Brick\Math\BigDecimal;
use DateTime;
use Kedniko\Vivy\Core\Constants;
use Kedniko\Vivy\Core\Helpers;
use Kedniko\Vivy\Core\LinkedList;
use Kedniko\Vivy\Core\Middleware;
use Kedniko\Vivy\Core\Options;
use Kedniko\Vivy\Core\Rule;
use Kedniko\Vivy\Core\Undefined;
use Kedniko\Vivy\Core\Validated;
use Kedniko\Vivy\Exceptions\VivyMiddlewareNotFoundException;
use Kedniko\Vivy\Messages\RuleMessage;
use Kedniko\Vivy\Rules\RuleFunctions;
use Kedniko\Vivy\Support\Str;
use Kedniko\Vivy\Types\Type;
use Kedniko\Vivy\V;

class Rules
{
    const ID_REQUIRED = 'required';
    const ID_NOT_EMPTY_STRING = 'notEmptyString';
    const ID_NOT_NULL = 'notNull';
    const ID_NULL = 'null';
    const ID_GROUP = 'group';
    const ID_EACH = 'each';
    const ID_OR = 'or';
    const ID_AND = 'and';

    const ID_NOT_FALSY = 'notFalsy';
    const ID_EMPTY_STRING = 'emptyString';
    const ID_MIN_DATE = 'minDate';
    const ID_MAX_DATE = 'maxDate';
    const ID_DATE_BETWEEN = 'dateBetween';
    const ID_DATE_NOT_BETWEEN = 'dateNotBetween';

    const ID_STRING = 'string';
    const ID_INTSTRING = 'intString';
    const ID_DIGITS_STRING = 'digitsString';
    const ID_INTBOOL = 'intBool';
    const ID_FILE = 'file';
    const ID_INT = 'int';
    const ID_FLOAT = 'float';
    const ID_NUMBER = 'number';
    const ID_FLOAT_OR_INT = 'floatOrInt';
    const ID_FLOAT_STRING = 'floatString';
    const ID_BOOL = 'bool';
    const ID_BOOL_STRING = 'boolString';
    const ID_EMAIL = 'email';
    const ID_PHONE = 'phone';
    const ID_DATE = 'date';
    const ID_MIN = 'min';
    const ID_MAX = 'max';
    const ID_ARRAY = 'array';
    const ID_SCALAR = 'scalar';
    const ID_IN_ARRAY = 'inArray';
    const ID_NOT_IN_ARRAY = 'notInArray';
    const ID_UNDEFINED = 'undefined';
    const ID_SET_VALUE = 'setValue';
    const ID_INT_STRING = 'intString';

    public static function getInvisibleKeys()
    {
        return [
            self::ID_GROUP,
            self::ID_EACH,
        ];
    }

    /**
     * @param string $id
     */
    public static function call($id)
    {
        $options = null;
        $args = [];

        foreach (array_slice(func_get_args(), 1) as $index => $arg) {
            if ($arg instanceof Options) {
                $options = $arg;
            } else {
                $args[] = $arg;
            }
        }

        $options = Helpers::getOptions($options);

        if (!array_key_exists($id, V::$registeredMiddlewares)) {
            throw new VivyMiddlewareNotFoundException("Middleware \"{$id}\" not found", 1);
        }

        $rule = V::$registeredMiddlewares[$id];
        $rule->setArgs($args);
        if ($options->getErrorMessage() !== null) {
            $rule->setErrorMessage($options->getErrorMessage());
        }
        $rule->setStopOnFailure($options->getStopOnFailure());
        return $rule;
    }

    public static function required($errormessage = null)
    {
        $ruleID = Rules::ID_REQUIRED;
        $ruleFn = null;
        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.' . $ruleID);
        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function notFalsy($errormessage = null)
    {
        $ruleID = Rules::ID_NOT_FALSY;
        $ruleFn = function (Context $c) {
            return $c->value ? true : false;
        };
        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.' . $ruleID);
        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function notNull($errormessage = null)
    {
        $ruleID = Rules::ID_NOT_NULL;
        $ruleFn = function (Context $c) {
            return $c->value !== null;
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.' . $ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function null($errormessage = null)
    {
        $ruleID = Rules::ID_NULL;
        $ruleFn = function (Context $c) {
            return $c->value === null;
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.' . $ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function notEmptyString($trim = false, $errormessage = null)
    {
        $ruleID = Rules::ID_NOT_EMPTY_STRING;
        $ruleFn = function (Context $c) use ($trim) {
            $trim = Helpers::valueOrFunction($trim, $c);
            $value = $c->value;
            if (is_string($value) && $trim) {
                $value = trim($value);
            }
            return $c->value !== '';
        };
        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.' . $ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function emptyString($trim = false, $errormessage = null)
    {
        $ruleID = Rules::ID_EMPTY_STRING;
        $ruleFn = function (Context $c) use ($trim) {
            $trim = Helpers::valueOrFunction($trim, $c);
            $value = $c->value;
            if (is_string($value) && $trim) {
                $value = trim($value);
            }
            return $c->value === '';
        };
        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.' . $ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function string($allowEmpty = true, $trimBeforeCheckEmpty = false, $toTrim = false, $errormessage = null)
    {
        $ruleID = self::ID_STRING;
        $ruleFn = function (Context $c) use ($allowEmpty, $toTrim, $trimBeforeCheckEmpty) {
            $value = $c->value;

            $allowEmpty = Helpers::valueOrFunction($allowEmpty, $c);
            $toTrim = Helpers::valueOrFunction($toTrim, $c);
            $trimBeforeCheckEmpty = Helpers::valueOrFunction($trimBeforeCheckEmpty, $c);

            if (!is_string($value)) {
                return false;
            }

            if ($trimBeforeCheckEmpty) {
                $value = trim($value);
            }

            if (!$allowEmpty && $value === '') {
                return false;
            }

            if ($toTrim) {
                $c->value = trim($c->value);
            }

            return true;
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.' . $ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function digitsString($trim = false, $errormessage = null)
    {
        $ruleID = self::ID_DIGITS_STRING;
        $ruleFn = function (Context $c) use ($trim) {
            $trim = Helpers::valueOrFunction($trim, $c);
            $value = $c->value;
            if (!is_string($value)) {
                return false;
            }

            if ($trim) {
                $value = trim($value);
            }
            return preg_match(Constants::REGEX_DIGITS, strval($value), $matches) === 1;
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.' . $ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function intString($trim = false, $errormessage = null)
    {
        $ruleID = self::ID_INTSTRING;
        $ruleFn = function (Context $c) use ($trim) {
            $trim = Helpers::valueOrFunction($trim, $c);
            $value = $c->value;
            $isTypeIntString = self::isTypeIntString($trim, $value);
            return $isTypeIntString;
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.' . $ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function boolString($errormessage = null)
    {
        $ruleID = self::ID_BOOL_STRING;
        $ruleFn = function (Context $c) {
            return in_array($c->value, ['true', 'false'], true);
        };
        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.' . $ruleID);
        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function intBool($strict = false, $errormessage = null)
    {
        $ruleID = self::ID_INTBOOL;
        $ruleFn = function (Context $c) use ($strict) {
            $strict = Helpers::valueOrFunction($strict, $c);
            if ($strict) {
                return in_array($c->value, [0, 1], true);
            } else {
                return is_int($c->value);
            }
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.' . $ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function file($errormessage = null)
    {
        $ruleID = self::ID_FILE;
        $ruleFn = function (Context $c) {
            $value = $c->value;

            $isOfTypeFile = isset($value['name']) &&
                isset($value['full_path']) &&
                isset($value['type']) &&
                isset($value['tmp_name']) &&
                isset($value['error']) &&
                isset($value['size']);

            if (!$isOfTypeFile) {
                return false;
            }

            return true;
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.' . $ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function int($errormessage = null)
    {
        $ruleID = self::ID_INT;
        $ruleFn = function (Context $c) {
            return is_int($c->value);
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.' . $ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    // /**
    //  * Test method
    //  * @param mixed $errormessage
    //  * @return Rule
    //  */
    // public static function intWithClass($errormessage = null)
    // {
    // 	$ruleID = RuleFunctions::RULE_NUMBER_INT;
    // 	$ruleFn = RuleFunctions::class . '::' . $ruleID;
    // 	$errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.' . $ruleID);
    // 	return new Rule($ruleID, $ruleFn, $errormessage);
    // }

    public static function floatOrInt($errormessage = null)
    {
        $ruleID = self::ID_FLOAT_OR_INT;
        $ruleFn = function (Context $c) {
            return is_float($c->value) || is_int($c->value);
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.' . $ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function number($errormessage = null)
    {
        $ruleID = self::ID_NUMBER;
        $ruleFn = function (Context $c) {
            return !is_string($c->value) && is_numeric($c->value);
        };
        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.' . $ruleID);
        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function float($strictFloat = true, $errormessage = null)
    {
        $ruleID = self::ID_FLOAT;
        $ruleFn = function (Context $c) use ($strictFloat) {
            $strictFloat = Helpers::valueOrFunction($strictFloat, $c);
            if ($strictFloat) {
                return is_float($c->value);
            }
            return is_float($c->value) || is_int($c->value);
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.' . $ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function floatString($strictFloat = true, $trim = false, $errormessage = null)
    {
        $ruleID = self::ID_FLOAT_STRING;
        $ruleFn = function (Context $c) use ($strictFloat, $trim) {
            $strictFloat = Helpers::valueOrFunction($strictFloat, $c);
            $trim = Helpers::valueOrFunction($trim, $c);
            $isTypeFloatString = self::isTypeFloatString($trim, $strictFloat, $c->value);
            return $isTypeFloatString;
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.' . $ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    private static function isTypeIntString($trim, $value)
    {
        if (!is_string($value)) {
            return false;
        }

        if ($trim) {
            $value = trim($value);
        }

        $isTypeIntString = preg_match(Constants::REGEX_INTEGER_POSITIVE_OR_NEGATIVE, strval($value), $matches) === 1;
        return $isTypeIntString;
    }

    private static function isTypeFloatString($trim, $strictFloat, $value)
    {
        if (!is_string($value)) {
            return false;
        }

        if ($trim) {
            $value = trim($value);
        }

        $value = strval($value);

        $isTypeFloatString = preg_match(Constants::REGEX_FLOAT_POSITIVE_OR_NEGATIVE, $value, $matches) === 1;

        if ($strictFloat) {
            return $isTypeFloatString;
        }

        $isTypeIntString = preg_match(Constants::REGEX_INTEGER_POSITIVE_OR_NEGATIVE, $value, $matches) === 1;

        return $isTypeFloatString || $isTypeIntString;
    }

    public static function numberString($trim = false, $errormessage = null)
    {
        $ruleID = self::ID_FLOAT_STRING;
        $ruleFn = function (Context $c) use ($trim) {
            $trim = Helpers::valueOrFunction($trim, $c);
            if (!is_string($c->value)) {
                return false;
            }
            $isTypeIntString = self::isTypeIntString($trim, $c->value);

            $isTypeFloatString = self::isTypeFloatString($trim, false, $c->value);

            return $isTypeIntString || $isTypeFloatString;
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.' . $ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function undefined($errormessage = null)
    {
        $ruleID = self::ID_UNDEFINED;
        $ruleFn = fn () => Undefined::instance();

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.' . $ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function bool($errormessage = null)
    {
        $ruleID = self::ID_BOOL;
        $ruleFn = function (Context $c) {
            return is_bool($c->value);
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.' . $ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    /**
     * @return Rule
     */
    public static function email($errormessage = null)
    {
        $ruleID = self::ID_EMAIL;
        $ruleFn = function (Context $c) {
            if (!$c->value || !is_string($c->value)) {
                return false;
            }
            return preg_match(Constants::REGEX_MAIL, $c->value) === 1;
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.' . $ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function equals($value, $strict = true, $errormessage = null)
    {
        $ruleID = 'equals';
        $ruleFn = function (Context $c) use ($value, $strict) {
            $value = Helpers::valueOrFunction($value, $c);
            $strict = Helpers::valueOrFunction($strict, $c);
            return $strict === true ? $c->value === $value : $c->value == $value;
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.' . $ruleID);
        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function notEquals($value, $strict, $errormessage = null)
    {
        $ruleID = 'notEquals';
        $ruleFn = function (Context $c) use ($value, $strict) {
            $value = Helpers::valueOrFunction($value, $c);
            $strict = Helpers::valueOrFunction($strict, $c);
            return $strict === true ? $c->value !== $value : $c->value != $value;
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.' . $ruleID);
        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function phone($errormessage = null)
    {
        $ruleID = self::ID_PHONE;
        $ruleFn = function (Context $c) {
            if (!$c->value || !is_string($c->value)) {
                return false;
            }
            return preg_match(Constants::REGEX_CELLPHONE_WITH_OPTIONAL_PREFIX, $c->value) === 1;
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.' . $ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    /**
     * @param string $format https://www.php.net/manual/en/datetime.format.php
     * @param string|callable|null $errormessage
     *
     */
    public static function date($format = 'Y-m-d', $errormessage = null)
    {
        $ruleID = self::ID_DATE;
        $ruleFn = function (Context $c) use ($format) {
            $format = Helpers::valueOrFunction($format, $c);
            if (!$c->value || !is_string($c->value)) {
                return false;
            }

            $date = $c->value;

            $d = DateTime::createFromFormat($format, $date);
            return $d && $d->format($format) === $date;
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.' . $ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function min($min, $errormessage = null)
    {
        $ruleID = self::ID_MIN;
        $ruleFn = function (Context $c) use ($min) {
            try {
                $min = Helpers::valueOrFunction($min, $c);
                return BigDecimal::of($c->value)->isGreaterThanOrEqualTo(BigDecimal::of($min));
            } catch (\Throwable $th) {
                return false;
            }
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.' . $ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function max($max, $errormessage = null)
    {
        $ruleID = self::ID_MAX;
        $ruleFn = function (Context $c) use ($max) {
            try {
                $max = Helpers::valueOrFunction($max, $c);
                return BigDecimal::of($c->value)->isLessThanOrEqualTo(BigDecimal::of($max));
            } catch (\Throwable $th) {
                return false;
            }
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.' . $ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function minDate(Datetime $date, $sourceFormat = 'Y-m-d', $errormessage = null)
    {
        $ruleID = self::ID_MIN_DATE;
        $ruleFn = function (Context $c) use ($date, $sourceFormat) {
            $date = Helpers::valueOrFunction($date, $c);
            $sourceFormat = Helpers::valueOrFunction($sourceFormat, $c);
            if ($c->value instanceof DateTime) {
                $given = $c->value;
            } else {
                $given = (new DateTime())->createFromFormat($sourceFormat, $c->value)->setTime(0, 0, 0, 0);
            }

            return $date <= $given;
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.' . $ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function maxDate(Datetime $date, $sourceFormat = 'Y-m-d', $errormessage = null)
    {
        $ruleID = self::ID_MAX_DATE;
        $ruleFn = function (Context $c) use ($date, $sourceFormat) {
            $date = Helpers::valueOrFunction($date, $c);
            $sourceFormat = Helpers::valueOrFunction($sourceFormat, $c);
            if ($c->value instanceof DateTime) {
                $given = $c->value;
            } else {
                $given = (new DateTime())->createFromFormat($sourceFormat, $c->value)->setTime(0, 0, 0, 0);
            }

            return $given <= $date;
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.' . $ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function between(Datetime $minDate, Datetime $maxDate, $sourceFormat = 'Y-m-d', $errormessage = null)
    {
        $ruleID = self::ID_DATE_BETWEEN;
        $ruleFn = function (Context $c) use ($minDate, $maxDate, $sourceFormat) {
            $minDate = Helpers::valueOrFunction($minDate, $c);
            $maxDate = Helpers::valueOrFunction($maxDate, $c);
            $sourceFormat = Helpers::valueOrFunction($sourceFormat, $c);
            if ($c->value instanceof DateTime) {
                $given = $c->value;
            } else {
                $given = (new DateTime())->createFromFormat($sourceFormat, $c->value)->setTime(0, 0, 0, 0);
            }

            return  $minDate <= $given && $given <= $maxDate;
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.' . $ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function notBetween(Datetime $minDate, Datetime $maxDate, $sourceFormat = 'Y-m-d', $errormessage = null)
    {
        $ruleID = self::ID_DATE_BETWEEN;
        $ruleFn = function (Context $c) use ($minDate, $maxDate, $sourceFormat) {
            $minDate = Helpers::valueOrFunction($minDate, $c);
            $maxDate = Helpers::valueOrFunction($maxDate, $c);
            $sourceFormat = Helpers::valueOrFunction($sourceFormat, $c);
            if ($c->value instanceof DateTime) {
                $given = $c->value;
            } else {
                $given = (new DateTime())->createFromFormat($sourceFormat, $c->value)->setTime(0, 0, 0, 0);
            }

            return  !($minDate <= $given && $given <= $maxDate);
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.' . $ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function array($errormessage = null)
    {
        $ruleID = self::ID_ARRAY;
        $ruleFn = function (Context $c) {
            return is_array($c->value);
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.' . $ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function scalar($errormessage = null)
    {
        $ruleID = self::ID_SCALAR;
        $ruleFn = function (Context $c) {
            return is_scalar($c->value);
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.' . $ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function in($array, $strict = true, $errormessage = null)
    {
        $ruleID = self::ID_IN_ARRAY;
        $ruleFn = function (Context $c) use ($array, $strict) {
            $array = Helpers::valueOrFunction($array, $c);
            $strict = Helpers::valueOrFunction($strict, $c);
            return in_array($c->value, $array, $strict);
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.' . $ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function notInArray($array, $errormessage = null)
    {
        $ruleID = self::ID_NOT_IN_ARRAY;
        $ruleFn = function (Context $c) use ($array) {
            $array = Helpers::valueOrFunction($array, $c);
            return !in_array($c->value, $array, true);
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.' . $ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function ruleEndsWith(string $endsWith, $ignoreCase = true, $errormessage = null)
    {
        $ruleID = 'endsWith';
        $ruleFn = function (Context $c) use ($endsWith, $ignoreCase) {
            $endsWith = Helpers::valueOrFunction($endsWith, $c);
            $ignoreCase = Helpers::valueOrFunction($ignoreCase, $c);
            if (!$c->value || !is_string($c->value)) {
                return false;
            }
            return Str::endsWith($c->value, $endsWith, $ignoreCase);
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage("default.{$ruleID}");

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function ruleStartsWith(string $startsWith, $ignoreCase = true, $errormessage = null)
    {
        $ruleID = 'startsWith';
        $ruleFn = function (Context $c) use ($startsWith, $ignoreCase) {
            $startsWith = Helpers::valueOrFunction($startsWith, $c);
            $ignoreCase = Helpers::valueOrFunction($ignoreCase, $c);
            if (!$c->value || !is_string($c->value)) {
                return false;
            }
            return Str::startsWith($c->value, $startsWith, $ignoreCase);
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage("default.{$ruleID}");

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function ruleContains(string $contains, $ignoreCase = true, $errormessage = null)
    {
        $ruleID = 'contains';
        $ruleFn = function (Context $c) use ($contains, $ignoreCase) {
            $contains = Helpers::valueOrFunction($contains, $c);
            $ignoreCase = Helpers::valueOrFunction($ignoreCase, $c);
            if (!$c->value || !is_string($c->value)) {
                return false;
            }
            return Str::contains($c->value, $contains, $ignoreCase);
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage("default.{$ruleID}");

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function ruleMinLength($length, $errormessage = null)
    {
        $ruleID = 'minLength';
        $ruleFn = function (Context $c) use ($length) {
            $length = Helpers::valueOrFunction($length, $c);
            if (!$c->value || !is_string($c->value)) {
                return false;
            }
            return strlen($c->value) >= $length;
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage("default.{$ruleID}");

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function ruleMaxLength($length, $errormessage = null)
    {
        $ruleID = 'maxLength';
        $ruleFn = function (Context $c) use ($length) {
            $length = Helpers::valueOrFunction($length, $c);
            if (!$c->value || !is_string($c->value)) {
                return false;
            }
            return strlen($c->value) <= $length;
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage("default.{$ruleID}");

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function ruleLength(int $length, $errormessage = null)
    {
        $ruleID = 'length';
        $ruleFn = function (Context $c) use ($length) {
            $length = Helpers::valueOrFunction($length, $c);
            if (!$c->value || !is_string($c->value)) {
                return false;
            }
            return strlen($c->value) === $length;
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage("default.{$ruleID}");

        return new Rule($ruleID, $ruleFn, $errormessage);
    }
}
