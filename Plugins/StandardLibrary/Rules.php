<?php

namespace Kedniko\Vivy\Plugins\StandardLibrary;

use Brick\Math\BigDecimal;
use DateTime;
use Kedniko\Vivy\Contracts\ContextInterface;
use Kedniko\Vivy\Core\Constants;
use Kedniko\Vivy\Core\Helpers;
use Kedniko\Vivy\Core\Options;
use Kedniko\Vivy\Core\Rule;
use Kedniko\Vivy\Core\Undefined;
use Kedniko\Vivy\Exceptions\VivyMiddlewareNotFoundException;
use Kedniko\Vivy\Messages\RuleMessage;
use Kedniko\Vivy\Support\Str;

final class Rules
{
    public const ID_REQUIRED = 'required';

    public const ID_NOT_EMPTY_STRING = 'notEmptyString';

    public const ID_NOT_NULL = 'notNull';

    public const ID_NULL = 'null';

    public const ID_GROUP = 'group';

    public const ID_EACH = 'each';

    public const ID_OR = 'or';

    public const ID_AND = 'and';

    public const ID_NOT_FALSY = 'notFalsy';

    public const ID_EMPTY_STRING = 'emptyString';

    public const ID_MIN_DATE = 'minDate';

    public const ID_MAX_DATE = 'maxDate';

    public const ID_DATE_BETWEEN = 'dateBetween';

    public const ID_DATE_NOT_BETWEEN = 'dateNotBetween';

    public const ID_STRING = 'string';

    public const ID_INTSTRING = 'intString';

    public const ID_DIGITS_STRING = 'digitsString';

    public const ID_INTBOOL = 'intBool';

    public const ID_FILE = 'file';

    public const ID_INT = 'int';

    public const ID_FLOAT = 'float';

    public const ID_NUMBER = 'number';

    public const ID_FLOAT_OR_INT = 'floatOrInt';

    public const ID_FLOAT_STRING = 'floatString';

    public const ID_BOOL = 'bool';

    public const ID_BOOL_STRING = 'boolString';

    public const ID_EMAIL = 'email';

    public const ID_PHONE = 'phone';

    public const ID_DATE = 'date';

    public const ID_MIN = 'min';

    public const ID_MAX = 'max';

    public const ID_ARRAY = 'array';

    public const ID_SCALAR = 'scalar';

    public const ID_IN_ARRAY = 'inArray';

    public const ID_NOT_IN_ARRAY = 'notInArray';

    public const ID_UNDEFINED = 'undefined';

    public const ID_SET_VALUE = 'setValue';

    public const ID_INT_STRING = 'intString';

    public static function getInvisibleKeys(): array
    {
        return [
            self::ID_GROUP,
            self::ID_EACH,
        ];
    }

    // public static function call(string $id): Rule
    // {
    //     $options = null;
    //     $args = [];

    //     foreach (array_slice(func_get_args(), 1) as $arg) {
    //         if ($arg instanceof Options) {
    //             $options = $arg;
    //         } else {
    //             $args[] = $arg;
    //         }
    //     }

    //     $options = Helpers::getOptions($options);

    //     if (!V::$magicCaller->hasId($id)) {
    //         throw new VivyMiddlewareNotFoundException("Middleware \"{$id}\" not found", 1);
    //     }

    //     $rule = V::$magicCaller->getId($id);
    //     $rule->setArgs($args);
    //     if ($options->getErrorMessage() !== null) {
    //         $rule->setErrorMessage($options->getErrorMessage());
    //     }
    //     $rule->setStopOnFailure($options->getStopOnFailure());

    //     return $rule;
    // }

    public static function required(string|callable $errormessage = null): Rule
    {
        $ruleID = Rules::ID_REQUIRED;
        $ruleFn = null;
        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.'.$ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function notFalsy(string|callable $errormessage = null): Rule
    {
        $ruleID = Rules::ID_NOT_FALSY;
        $ruleFn = fn (ContextInterface $c): bool => (bool) $c->value;
        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.'.$ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function notNull(string|callable $errormessage = null): Rule
    {
        $ruleID = Rules::ID_NOT_NULL;
        $ruleFn = fn (ContextInterface $c): bool => $c->value !== null;

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.'.$ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function null(string|callable $errormessage = null): Rule
    {
        $ruleID = Rules::ID_NULL;
        $ruleFn = fn (ContextInterface $c): bool => $c->value === null;

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.'.$ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function notEmptyString(bool $trim = false, string|callable $errormessage = null): Rule
    {
        $ruleID = Rules::ID_NOT_EMPTY_STRING;
        $ruleFn = function (ContextInterface $c) use ($trim): bool {
            $trim = Helpers::valueOrFunction($trim, $c);
            $value = $c->value;
            if (! is_string($value)) {
                return $c->value !== '';
            }
            if (! $trim) {
                return $c->value !== '';
            }
            $value = trim($value);

            return $c->value !== '';
        };
        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.'.$ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function emptyString(bool $trim = false, string|callable $errormessage = null): Rule
    {
        $ruleID = Rules::ID_EMPTY_STRING;
        $ruleFn = function (ContextInterface $c) use ($trim): bool {
            $trim = Helpers::valueOrFunction($trim, $c);
            $value = $c->value;
            if (! is_string($value)) {
                return $c->value === '';
            }
            if (! $trim) {
                return $c->value === '';
            }
            $value = trim($value);

            return $c->value === '';
        };
        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.'.$ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function string(string|callable $errormessage = null): Rule
    {
        $ruleID = self::ID_STRING;
        $ruleFn = function (ContextInterface $c): bool {
            $value = $c->value;

            if (! is_string($value)) {
                return false;
            }

            return true;
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.'.$ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function digitsString(bool $trim = false, string|callable $errormessage = null): Rule
    {
        $ruleID = self::ID_DIGITS_STRING;
        $ruleFn = function (ContextInterface $c) use ($trim): bool {
            $trim = Helpers::valueOrFunction($trim, $c);
            $value = $c->value;
            if (! is_string($value)) {
                return false;
            }

            if ($trim) {
                $value = trim($value);
            }

            return preg_match(Constants::REGEX_DIGITS, $value, $matches) === 1;
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.'.$ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function intString(bool $trim = false, string|callable $errormessage = null): Rule
    {
        $ruleID = self::ID_INTSTRING;
        $ruleFn = function (ContextInterface $c) use ($trim) {
            $trim = Helpers::valueOrFunction($trim, $c);
            $value = $c->value;

            return self::isTypeIntString($trim, $value);
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.'.$ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function boolString(string|callable $errormessage = null): Rule
    {
        $ruleID = self::ID_BOOL_STRING;
        $ruleFn = fn (ContextInterface $c): bool => in_array($c->value, ['true', 'false'], true);
        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.'.$ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function intBool(bool $strict = false, string|callable $errormessage = null): Rule
    {
        $ruleID = self::ID_INTBOOL;
        $ruleFn = function (ContextInterface $c) use ($strict): bool|int {
            $strict = Helpers::valueOrFunction($strict, $c);
            if ($strict) {
                return in_array($c->value, [0, 1], true);
            }

            return is_int($c->value);
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.'.$ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function file(string|callable $errormessage = null): Rule
    {
        $ruleID = self::ID_FILE;
        $ruleFn = function (ContextInterface $c): bool {
            $value = $c->value;

            return isset($value['name']) &&
                isset($value['full_path']) &&
                isset($value['type']) &&
                isset($value['tmp_name']) &&
                isset($value['error']) &&
                isset($value['size']);
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.'.$ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function int(string|callable $errormessage = null): Rule
    {
        $ruleID = self::ID_INT;
        $ruleFn = fn (ContextInterface $c): bool => is_int($c->value);

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.'.$ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    // /**
    //  * Test method
    //  * @param mixed $errormessage
    //  * @return Rule
    //  */
    // public static function intWithClass(string|callable|null $errormessage = null)
    // {
    // 	$ruleID = RuleFunctions::RULE_NUMBER_INT;
    // 	$ruleFn = RuleFunctions::class . '::' . $ruleID;
    // 	$errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.' . $ruleID);
    // 	return new Rule($ruleID, $ruleFn, $errormessage);
    // }

    public static function floatOrInt(string|callable $errormessage = null): Rule
    {
        $ruleID = self::ID_FLOAT_OR_INT;
        $ruleFn = fn (ContextInterface $c): bool => is_float($c->value) || is_int($c->value);

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.'.$ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function number(string|callable $errormessage = null): Rule
    {
        $ruleID = self::ID_NUMBER;
        $ruleFn = fn (ContextInterface $c): bool => ! is_string($c->value) && is_numeric($c->value);
        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.'.$ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function float(bool $strictFloat = true, string|callable $errormessage = null): Rule
    {
        $ruleID = self::ID_FLOAT;
        $ruleFn = function (ContextInterface $c) use ($strictFloat): bool {
            $strictFloat = Helpers::valueOrFunction($strictFloat, $c);
            if ($strictFloat) {
                return is_float($c->value);
            }

            return is_float($c->value) || is_int($c->value);
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.'.$ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function floatString(bool $strictFloat = true, $trim = false, string|callable $errormessage = null): Rule
    {
        $ruleID = self::ID_FLOAT_STRING;
        $ruleFn = function (ContextInterface $c) use ($strictFloat, $trim) {
            $strictFloat = Helpers::valueOrFunction($strictFloat, $c);
            $trim = Helpers::valueOrFunction($trim, $c);

            return self::isTypeFloatString($trim, $strictFloat, $c->value);
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.'.$ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    private static function isTypeIntString(bool $trim, $value): bool
    {
        if (! is_string($value)) {
            return false;
        }

        if ($trim) {
            $value = trim($value);
        }

        return preg_match(Constants::REGEX_INTEGER_POSITIVE_OR_NEGATIVE, $value, $matches) === 1;
    }

    private static function isTypeFloatString(bool $trim, $strictFloat, $value)
    {
        if (! is_string($value)) {
            return false;
        }

        if ($trim) {
            $value = trim($value);
        }

        $isTypeFloatString = preg_match(Constants::REGEX_FLOAT_POSITIVE_OR_NEGATIVE, $value, $matches) === 1;

        if ($strictFloat) {
            return $isTypeFloatString;
        }

        $isTypeIntString = preg_match(Constants::REGEX_INTEGER_POSITIVE_OR_NEGATIVE, $value, $matches) === 1;

        return $isTypeFloatString || $isTypeIntString;
    }

    public static function numberString(bool $trim = false, string|callable $errormessage = null): Rule
    {
        $ruleID = self::ID_FLOAT_STRING;
        $ruleFn = function (ContextInterface $c) use ($trim): bool {
            $trim = Helpers::valueOrFunction($trim, $c);
            if (! is_string($c->value)) {
                return false;
            }
            $isTypeIntString = self::isTypeIntString($trim, $c->value);

            $isTypeFloatString = self::isTypeFloatString($trim, false, $c->value);

            return $isTypeIntString || $isTypeFloatString;
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.'.$ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function undefined(string|callable $errormessage = null): Rule
    {
        $ruleID = self::ID_UNDEFINED;
        $ruleFn = fn () => Undefined::instance();

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.'.$ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function bool(string|callable $errormessage = null): Rule
    {
        $ruleID = self::ID_BOOL;
        $ruleFn = fn (ContextInterface $c): bool => is_bool($c->value);

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.'.$ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function email(string|callable $errormessage = null): Rule
    {
        $ruleID = self::ID_EMAIL;
        $ruleFn = function (ContextInterface $c): bool {
            if (! $c->value) {
                return false;
            }
            if (! is_string($c->value)) {
                return false;
            }

            return preg_match(Constants::REGEX_MAIL, $c->value) === 1;
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.'.$ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function equals(mixed $value, bool $strict = true, string|callable $errormessage = null): Rule
    {
        $ruleID = 'equals';
        $ruleFn = function (ContextInterface $c) use ($value, $strict): bool {
            $value = Helpers::valueOrFunction($value, $c);
            $strict = Helpers::valueOrFunction($strict, $c);

            return $strict === true ? $c->value === $value : $c->value == $value;
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.'.$ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function notEquals(mixed $value, bool $strict, string|callable $errormessage = null): Rule
    {
        $ruleID = 'notEquals';
        $ruleFn = function (ContextInterface $c) use ($value, $strict): bool {
            $value = Helpers::valueOrFunction($value, $c);
            $strict = Helpers::valueOrFunction($strict, $c);

            return $strict === true ? $c->value !== $value : $c->value != $value;
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.'.$ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function phone(string|callable $errormessage = null): Rule
    {
        $ruleID = self::ID_PHONE;
        $ruleFn = function (ContextInterface $c): bool {
            if (! $c->value) {
                return false;
            }
            if (! is_string($c->value)) {
                return false;
            }

            return preg_match(Constants::REGEX_CELLPHONE_WITH_OPTIONAL_PREFIX, $c->value) === 1;
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.'.$ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    /**
     * @param  string  $format https://www.php.net/manual/en/datetime.format.php
     */
    public static function date(string $format = 'Y-m-d', string|callable $errormessage = null): Rule
    {
        $ruleID = self::ID_DATE;
        $ruleFn = function (ContextInterface $c) use ($format): bool {
            $format = Helpers::valueOrFunction($format, $c);
            if (! $c->value) {
                return false;
            }
            if (! is_string($c->value)) {
                return false;
            }

            $date = $c->value;

            $d = DateTime::createFromFormat($format, $date);

            return $d && $d->format($format) === $date;
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.'.$ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function min(\Brick\Math\BigNumber|int|float|string $min, string|callable $errormessage = null): Rule
    {
        $ruleID = self::ID_MIN;
        $ruleFn = function (ContextInterface $c) use ($min): bool {
            try {
                $min = Helpers::valueOrFunction($min, $c);

                return BigDecimal::of($c->value)->isGreaterThanOrEqualTo(BigDecimal::of($min));
            } catch (\Throwable) {
                return false;
            }
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.'.$ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function max(\Brick\Math\BigNumber|int|float|string $max, string|callable $errormessage = null): Rule
    {
        $ruleID = self::ID_MAX;
        $ruleFn = function (ContextInterface $c) use ($max): bool {
            try {
                $max = Helpers::valueOrFunction($max, $c);

                return BigDecimal::of($c->value)->isLessThanOrEqualTo(BigDecimal::of($max));
            } catch (\Throwable) {
                return false;
            }
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.'.$ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function minDate(Datetime $date, string $sourceFormat = 'Y-m-d', string|callable $errormessage = null): Rule
    {
        $ruleID = self::ID_MIN_DATE;
        $ruleFn = function (ContextInterface $c) use ($date, $sourceFormat): bool {
            $date = Helpers::valueOrFunction($date, $c);
            $sourceFormat = Helpers::valueOrFunction($sourceFormat, $c);
            if ($c->value instanceof DateTime) {
                $given = $c->value;
            } else {
                $given = (new DateTime())->createFromFormat($sourceFormat, $c->value)->setTime(0, 0, 0, 0);
            }

            return $date <= $given;
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.'.$ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function maxDate(Datetime $date, string $sourceFormat = 'Y-m-d', string|callable $errormessage = null): Rule
    {
        $ruleID = self::ID_MAX_DATE;
        $ruleFn = function (ContextInterface $c) use ($date, $sourceFormat): bool {
            $date = Helpers::valueOrFunction($date, $c);
            $sourceFormat = Helpers::valueOrFunction($sourceFormat, $c);
            if ($c->value instanceof DateTime) {
                $given = $c->value;
            } else {
                $given = (new DateTime())->createFromFormat($sourceFormat, $c->value)->setTime(0, 0, 0, 0);
            }

            return $given <= $date;
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.'.$ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function between(Datetime $minDate, Datetime $maxDate, string $sourceFormat = 'Y-m-d', string|callable $errormessage = null): Rule
    {
        $ruleID = self::ID_DATE_BETWEEN;
        $ruleFn = function (ContextInterface $c) use ($minDate, $maxDate, $sourceFormat): bool {
            $minDate = Helpers::valueOrFunction($minDate, $c);
            $maxDate = Helpers::valueOrFunction($maxDate, $c);
            $sourceFormat = Helpers::valueOrFunction($sourceFormat, $c);
            if ($c->value instanceof DateTime) {
                $given = $c->value;
            } else {
                $given = (new DateTime())->createFromFormat($sourceFormat, $c->value)->setTime(0, 0, 0, 0);
            }

            return $minDate <= $given && $given <= $maxDate;
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.'.$ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function notBetween(Datetime $minDate, Datetime $maxDate, string $sourceFormat = 'Y-m-d', string|callable $errormessage = null): Rule
    {
        $ruleID = self::ID_DATE_BETWEEN;
        $ruleFn = function (ContextInterface $c) use ($minDate, $maxDate, $sourceFormat): bool {
            $minDate = Helpers::valueOrFunction($minDate, $c);
            $maxDate = Helpers::valueOrFunction($maxDate, $c);
            $sourceFormat = Helpers::valueOrFunction($sourceFormat, $c);
            if ($c->value instanceof DateTime) {
                $given = $c->value;
            } else {
                $given = (new DateTime())->createFromFormat($sourceFormat, $c->value)->setTime(0, 0, 0, 0);
            }

            return ! ($minDate <= $given && $given <= $maxDate);
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.'.$ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function array(string|callable $errormessage = null): Rule
    {
        $ruleID = self::ID_ARRAY;
        $ruleFn = fn (ContextInterface $c): bool => is_array($c->value);

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.'.$ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function scalar(string|callable $errormessage = null): Rule
    {
        $ruleID = self::ID_SCALAR;
        $ruleFn = fn (ContextInterface $c): bool => is_scalar($c->value);

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.'.$ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function in(array $array, bool $strict = true, string|callable $errormessage = null): Rule
    {
        $ruleID = self::ID_IN_ARRAY;
        $ruleFn = function (ContextInterface $c) use ($array, $strict): bool {
            $array = Helpers::valueOrFunction($array, $c);
            $strict = Helpers::valueOrFunction($strict, $c);

            return in_array($c->value, $array, $strict);
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.'.$ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function notInArray(array $array, string|callable $errormessage = null): Rule
    {
        $ruleID = self::ID_NOT_IN_ARRAY;
        $ruleFn = function (ContextInterface $c) use ($array): bool {
            $array = Helpers::valueOrFunction($array, $c);

            return ! in_array($c->value, $array, true);
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.'.$ruleID);

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function ruleEndsWith(string $endsWith, bool $ignoreCase = true, string|callable $errormessage = null): Rule
    {
        $ruleID = 'endsWith';
        $ruleFn = function (ContextInterface $c) use ($endsWith, $ignoreCase): bool {
            $endsWith = Helpers::valueOrFunction($endsWith, $c);
            $ignoreCase = Helpers::valueOrFunction($ignoreCase, $c);
            if (! $c->value) {
                return false;
            }
            if (! is_string($c->value)) {
                return false;
            }

            return Str::endsWith($c->value, $endsWith, $ignoreCase);
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage("default.{$ruleID}");

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function ruleStartsWith(string $startsWith, bool $ignoreCase = true, string|callable $errormessage = null): Rule
    {
        $ruleID = 'startsWith';
        $ruleFn = function (ContextInterface $c) use ($startsWith, $ignoreCase): bool {
            $startsWith = Helpers::valueOrFunction($startsWith, $c);
            $ignoreCase = Helpers::valueOrFunction($ignoreCase, $c);
            if (! $c->value) {
                return false;
            }
            if (! is_string($c->value)) {
                return false;
            }

            return Str::startsWith($c->value, $startsWith, $ignoreCase);
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage("default.{$ruleID}");

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function ruleContains(string $contains, bool $ignoreCase = true, string|callable $errormessage = null): Rule
    {
        $ruleID = 'contains';
        $ruleFn = function (ContextInterface $c) use ($contains, $ignoreCase): bool {
            $contains = Helpers::valueOrFunction($contains, $c);
            $ignoreCase = Helpers::valueOrFunction($ignoreCase, $c);
            if (! $c->value) {
                return false;
            }
            if (! is_string($c->value)) {
                return false;
            }

            return Str::contains($c->value, $contains, $ignoreCase);
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage("default.{$ruleID}");

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function ruleMinLength(int $length, string|callable $errormessage = null): Rule
    {
        $ruleID = 'minLength';
        $ruleFn = function (ContextInterface $c) use ($length): bool {
            $length = Helpers::valueOrFunction($length, $c);
            if (! $c->value) {
                return false;
            }
            if (! is_string($c->value)) {
                return false;
            }

            return strlen($c->value) >= $length;
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage("default.{$ruleID}");

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function ruleMaxLength(int $length, string|callable $errormessage = null): Rule
    {
        $ruleID = 'maxLength';
        $ruleFn = function (ContextInterface $c) use ($length): bool {
            $length = Helpers::valueOrFunction($length, $c);
            if (! $c->value) {
                return false;
            }
            if (! is_string($c->value)) {
                return false;
            }

            return strlen($c->value) <= $length;
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage("default.{$ruleID}");

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public static function ruleLength(int $length, string|callable $errormessage = null): Rule
    {
        $ruleID = 'length';
        $ruleFn = function (ContextInterface $c) use ($length): bool {
            $length = Helpers::valueOrFunction($length, $c);
            if (! $c->value) {
                return false;
            }
            if (! is_string($c->value)) {
                return false;
            }

            return strlen($c->value) === $length;
        };

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage("default.{$ruleID}");

        return new Rule($ruleID, $ruleFn, $errormessage);
    }
}
