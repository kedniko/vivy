<?php

namespace Kedniko\Vivy\Plugins\StandardLibrary;

use Kedniko\Vivy\Core\Options;
use Kedniko\Vivy\Interfaces\VivyPlugin;
use Kedniko\Vivy\Messages\RuleMessage;
use Kedniko\Vivy\Rules;
use Kedniko\Vivy\Types\Type;
use Kedniko\Vivy\TypesProxy\TypeProxy;
use Kedniko\Vivy\V;

final class StandardLibrary implements VivyPlugin
{
    public function register(): void
    {
        // V::register([
        // 	// [availableFor, name, callback, return],
        // 	[V::class, 'any', [self::class, 'any'], Type::class],

        // 	[[V::class, TypeAny::class], 'or', [self::class, 'or'], TypeOr::class],
        // 	[[V::class, TypeAny::class], 'group', [self::class, 'group'], TypeGroup::class],
        // 	[[TypeString::class], 'digits', [self::class, 'digits'], TypeString::class],
        // 	[[V::class, TypeString::class], 'date', [self::class, 'date'], TypeStringDate::class],
        // ]);
        // return;

        V::register([
            // [[availableFor], name, callback, return],
            [V::class, 'any', [self::class, 'any'], Type::class],

            [[V::class, TypeAny::class, Type::class], 'or', [self::class, 'or'], TypeOr::class],
            [[V::class, TypeAny::class], 'group', [self::class, 'group'], TypeGroup::class],

            [[V::class, TypeAny::class], 'file', [self::class, 'file'], TypeFile::class],
            [[V::class, TypeAny::class], 'int', [self::class, 'int'], TypeInt::class],
            [[V::class, TypeAny::class], 'bool', [self::class, 'bool'], TypeBool::class],
            [[V::class, TypeAny::class], 'float', [self::class, 'float'], TypeFloat::class],
            [[V::class, TypeAny::class], 'number', [self::class, 'number'], TypeNumber::class],
            [[V::class, TypeAny::class], 'make', [self::class, 'make'], TypeMake::class], //
            [[V::class, TypeAny::class], 'everything', [self::class, 'everything'], TypeEverything::class], //
            [[V::class, TypeAny::class], 'notFalsy', [self::class, 'notFalsy'], TypeNotFalsy::class], //
            [[V::class, TypeAny::class], 'notNull', [self::class, 'notNull'], TypeNotNull::class], //
            [[V::class, TypeAny::class], 'null', [self::class, 'null'], TypeNull::class],
            [[V::class, TypeAny::class], 'files', [self::class, 'files'], TypeFiles::class],
            [[V::class, TypeAny::class], 'array', [self::class, 'array'], TypeArray::class], //

            [[V::class, TypeAny::class], 'string', [self::class, 'string'], TypeString::class],
            [[V::class, TypeString::class, TypeAny::class], 'date', [self::class, 'date'], TypeStringDate::class],
            [[V::class, TypeAny::class], 'intString', [self::class, 'intString'], TypeStringInt::class],
            [[V::class, TypeAny::class], 'boolString', [self::class, 'boolString'], TypeStringBool::class],
            [[TypeString::class], 'digits', [self::class, 'digits'], TypeString::class],
            [[V::class, TypeAny::class], 'floatString', [self::class, 'floatString'], TypeStringFloat::class],
            [[V::class, TypeAny::class], 'numberString', [self::class, 'numberString'], TypeStringNumber::class],
            [[V::class, TypeAny::class], 'email', [self::class, 'email'], TypeStringEmail::class],
            [[V::class, TypeAny::class], 'emptyString', [self::class, 'emptyString'], TypeStringEmpty::class], //
            [[V::class, TypeAny::class], 'notEmptyString', [self::class, 'notEmptyString'], TypeString::class], //
            // [V::class, 'inArray', [Rules::class, 'inArray'], Type::class], //
            // [V::class, 'notInArray', [Rules::class, 'notInArray'], Type::class], //
            // [V::class, 'equals', [Rules::class, 'equals'], Type::class], //

            // [V::class, 'optional', [self::class, 'optional'], Type::class], // TODO
            [[V::class, TypeAny::class], 'undefined', [self::class, 'undefined'], TypeUndefined::class], //

            // casts
            [[V::class, TypeAny::class, Type::class], 'asAny', [self::class, 'asAny'], TypeAny::class],
            [[V::class, TypeAny::class, Type::class], 'asFile', [self::class, 'asFile'], TypeFile::class],
            [[V::class, TypeAny::class, Type::class], 'asArray', [self::class, 'asArray'], TypeArray::class],
            [[V::class, TypeAny::class, Type::class], 'asBool', [self::class, 'asBool'], TypeBool::class],
            [[V::class, TypeAny::class, Type::class], 'asDate', [self::class, 'asDate'], TypeStringDate::class],
            [[V::class, TypeAny::class, Type::class], 'asEmail', [self::class, 'asEmail'], TypeStringEmail::class],
            [[V::class, TypeAny::class, Type::class], 'asScalar', [self::class, 'asScalar'], TypeScalar::class],
            [[V::class, TypeAny::class, Type::class], 'asFloat', [self::class, 'asFloat'], TypeFloat::class],
            [[V::class, TypeAny::class, Type::class], 'asGroup', [self::class, 'asGroup'], TypeGroup::class],
            [[V::class, TypeAny::class, Type::class], 'asInt', [self::class, 'asInt'], TypeInt::class],
            [[V::class, TypeAny::class, Type::class], 'asIntString', [self::class, 'asIntString'], TypeStringInt::class],
            [[V::class, TypeAny::class, Type::class], 'asNumber', [self::class, 'asNumber'], TypeNumber::class],
            [[V::class, TypeAny::class, Type::class], 'asString', [self::class, 'asString'], TypeString::class],

            // [[RootType::class], 'asAny', [self::class, 'asAny'], RootType::class], //
            // [V::class, 'and', [self::class, 'and'], TypeOr::class],
            // [V::class, 'intWithClass', [self::class, 'intWithClass'], TypeInt::class],

        ]);
    }

    public static function any()
    {
        return function (Type|null $obj) {
            return (new Type())->from($obj);
        };
    }

    public static function email(Options $options = null)
    {
        return function (Type|null $obj) use ($options) {
            $options = Options::build($options, func_get_args());
            $type = (new TypeStringEmail())->from($obj);
            $type->addRule(Rules::email($options->getErrorMessage()), $options);

            return $type;
        };
    }

    /**
     * @param  array|callable  $setup
     */
    public static function group($setup = null, bool $stopOnFieldFailure = false, Options $options = null)
    {
        return function (Type|null $obj) use ($setup, $stopOnFieldFailure, $options) {
            $options = Options::build($options, func_get_args());
            $type = (new TypeGroup())->from($obj)->init($setup);
            $type->addRule(Rules::array($options->getErrorMessage()), $options);
            $type->addRule($type->getGroupRule($stopOnFieldFailure, $options->getErrorMessage()), $options);

            return $type;
        };
    }

    /**
     * @param  \Kedniko\Vivy\Types\Type[]  $types
     */
    public static function or(array $types, Options $options = null)
    {
        return function (Type|null $obj) use ($types, $options) {
            $options = Options::build($options, func_get_args());

            return (new TypeOr())->from($obj)->init($types, false, $options);
        };
    }

    // /**
    //  * @param \Kedniko\Vivy\Types\Type[] $types
    //  * @param Options|null $options
    //  */
    // public static function and(array $types, Options $options = null)
    // {
    // 	$options = Options::build($options, func_get_args());
    // 	$type = new TypeOr($types, false, $options);
    // 	return $type;
    // }

    /**
     * @param  Type[]  $types
     */
    public static function notIn($types, Options $options = null)
    {
        return function (Type|null $obj) use ($options, $types) {
            $options = Options::build($options, func_get_args());

            return (new TypeOr($types, true, $options))->from($obj);
        };
    }

    public static function file(Options $options = null)
    {
        return function (Type|null $obj) use ($options) {
            $options = Options::build($options, func_get_args());
            $type = (new TypeFile())->from($obj);
            $type->addRule(Rules::file($options->getErrorMessage()), $options);

            return $type;
        };
    }

    public static function int(Options $options = null)
    {
        return function (Type|null $obj) use ($options) {
            $options = Options::build($options, func_get_args());
            $type = (new TypeInt())->from($obj);
            $type->addRule(Rules::int($options->getErrorMessage()), $options);

            return $type;
        };
    }

    // public static function intWithClass(Options $options = null)
    // {
    // 	$options = Options::build($options, func_get_args());
    // 	$type = new TypeInt();
    // 	$type->addRule(Rules::intWithClass($options->getErrorMessage()), $options);
    // 	return $type;
    // }

    public static function bool(Options $options = null)
    {
        return function (Type|null $obj) use ($options) {
            $options = Options::build($options, func_get_args());
            $type = (new TypeBool())->from($obj);
            $type->addRule(Rules::bool($options->getErrorMessage()), $options);

            return $type;
        };
    }

    public static function float($strictFloat = false, Options $options = null)
    {
        return function (Type|null $obj) use ($strictFloat, $options): \Kedniko\Vivy\Plugins\StandardLibrary\TypeFloat {
            $options = Options::build($options, func_get_args());
            $type = new TypeFloat();
            $type->addRule(Rules::float($strictFloat, $options->getErrorMessage()), $options);

            return $type;
        };
    }

    public static function number(Options $options = null)
    {
        return function (Type|null $obj) use ($options) {
            $options = Options::build($options, func_get_args());
            $type = (new TypeNumber())->from($obj);
            $type->addRule(Rules::floatOrInt($options->getErrorMessage()), $options);

            return $type;
        };
    }

    public static function numberString(Options $options = null)
    {
        return function (Type|null $obj) use ($options) {
            $options = Options::build($options, func_get_args());
            $type = (new TypeStringNumber())->from($obj);
            $type->addRule(Rules::numberString($options->getErrorMessage()), $options);

            return $type;
        };
    }

    public static function string($allowEmptyString = false, $trimBeforeCheckEmpty = false, $toTrim = false, Options $options = null)
    {
        return function (Type|null $obj) use ($toTrim, $allowEmptyString, $options, $trimBeforeCheckEmpty) {
            $options = Options::build($options, func_get_args());
            $type = (new TypeString())->from($obj);
            $type->addRule(Rules::string($allowEmptyString, $trimBeforeCheckEmpty, $toTrim, $options->getErrorMessage()), $options);
            $type->allowEmptyString();

            return $type;
        };
    }

    public static function digits(Options $options = null)
    {
        return function (Type|null $obj) use ($options) {
            $options = Options::build($options, func_get_args());
            $type = (new TypeStringInt())->from($obj);
            $type->addRule(Rules::digitsString($options->getErrorMessage()), $options);

            return $type;
        };
    }

    public static function intString(Options $options = null)
    {
        return function (Type|null $obj) use ($options) {
            $options = Options::build($options, func_get_args());
            $type = (new TypeStringInt())->from($obj);
            $type->addRule(Rules::intString($options->getErrorMessage()), $options);

            return $type;
        };
    }

    public static function boolString(Options $options = null)
    {
        return function (Type|null $obj) use ($options) {
            $options = Options::build($options, func_get_args());
            $type = (new TypeStringBool())->from($obj);
            $type->addRule(Rules::boolString($options->getErrorMessage()), $options);

            return $type;
        };
    }

    public static function floatString($strict = true, $trim = false, Options $options = null)
    {
        return function (Type|null $obj) use ($strict, $trim, $options) {
            $options = Options::build($options, func_get_args());
            $type = (new TypeStringInt())->from($obj);
            $type->addRule(Rules::floatString($strict, $trim, $options->getErrorMessage()), $options);

            return $type;
        };
    }

    public static function date($format = 'Y-m-d', Options $options = null)
    {
        return function (Type|null $obj) use ($format, $options) {
            $options = Options::build($options, func_get_args());
            $type = (new TypeStringDate())->from($obj);
            (new TypeProxy($type))->setChildStateProperty('_extra.format', $format);
            $type->addRule(Rules::date($format, $options->getErrorMessage()), $options);

            return $type;
        };
    }

    public static function everything(Options $options = null)
    {
        return function (Type|null $obj) use ($options) {
            $options = Options::build($options, func_get_args());

            return (new Type())->from($obj);
        };
    }

    public static function notFalsy(Options $options = null)
    {
        return function (Type|null $obj) use ($options) {
            $options = Options::build($options, func_get_args());
            $type = (new Type())->from($obj);
            $type->addRule(Rules::notFalsy($options->getErrorMessage() ?: RuleMessage::getErrorMessage('default.notFalsy')), $options);

            return $type;
        };
    }

    // public static function allowEmptyString()
    // {
    //     $type = new TypeUnkown();
    //     // TODO
    //     $type->allowEmptyString();
    //     return $type;
    // }
    // public static function allowNullTODO()
    // {
    //     $type = new TypeUnkown();
    //     $type->allowNull();
    //     return $type;
    // }
    public static function notNull(Options $options = null)
    {
        return function (Type|null $obj) use ($options) {
            $options = Options::build($options, func_get_args());

            return (new Type())->from($obj);
        };
    }

    public static function null(Options $options = null)
    {
        return function (Type|null $obj) use ($options) {
            $options = Options::build($options, func_get_args());
            $type = (new TypeNull())->from($obj);
            $type->allowNull();
            $type->addRule(Rules::null($options->getErrorMessage() ?: RuleMessage::getErrorMessage('default.'.Rules::ID_NULL)), $options);

            return $type;
        };
    }

    public static function notEmptyString(Options $options = null)
    {
        return function (Type|null $obj) use ($options) {
            $options = Options::build($options, func_get_args());
            $type = (new TypeString())->from($obj);
            $type->addRule(Rules::notEmptyString($options->getErrorMessage() ?: RuleMessage::getErrorMessage('default.'.Rules::ID_NOT_EMPTY_STRING)), $options);

            return $type;
        };
    }

    public static function emptyString(Options $options = null)
    {
        return function (Type|null $obj) use ($options) {
            $options = Options::build($options, func_get_args());
            $type = (new TypeStringEmpty())->from($obj);
            $type->addRule(Rules::emptyString($options->getErrorMessage() ?: RuleMessage::getErrorMessage('default.'.Rules::ID_EMPTY_STRING)), $options);
            $type->allowEmptyString();

            return $type;
        };
    }

    public static function optional(mixed $default = null)
    {
        return function (Type|null $obj) use ($default) {
            $type = (new Type())->from($obj);
            $type->removeRule(Rules::ID_REQUIRED);
            $type->state->setRequired(false);
            if (count(func_get_args())) {
                $type->state->setValueIfOptionalNotExists($default);
            }

            return $type;
        };
    }

    public static function undefined(bool $enableDafault = false, mixed $value = null, Options $options = null)
    {
        return function (Type|null $obj) use ($enableDafault, $value, $options) {
            $options = Options::build($options, func_get_args());
            $type = (new TypeUndefined())->from($obj);
            if (static::class === V::class) {
                $type->state->_extra = ['startsWithUndefined' => true];
            }

            if ($enableDafault) {
                $type->_extra = ['default' => $value];
            }
            $type->addRule(Rules::undefined(), $options);

            return $type;
        };
    }

    public static function files(Options $options = null)
    {
        return function (Type|null $obj) use ($options) {
            $options = Options::build($options, func_get_args());

            return (new TypeFiles())->from($obj);
        };
    }

    public static function array(Options $options = null)
    {
        return function (Type|null $obj) use ($options) {
            $options = Options::build($options, func_get_args());
            $type = (new TypeArray())->from($obj);
            $type->addRule(Rules::array($options->getErrorMessage()), $options);

            return $type;
        };
    }

    public static function make($data, $setup = null, bool $stopOnFieldFailure = false, Options $options = null)
    {
        $type = self::group($setup, $stopOnFieldFailure, $options);
        (new TypeProxy($type))->setData($data);

        return $type;
    }

    // casts

    public static function asFile()
    {
        return function (Type|null $obj) {
            return (new TypeFile())->from($obj);
        };
    }

    public static function asArray()
    {
        return function (Type|null $obj) {
            return (new TypeArray())->from($obj);
        };
    }

    public static function asBool()
    {
        return function (Type|null $obj) {
            return (new TypeBool())->from($obj);
        };
    }

    public static function asAny()
    {
        return function (Type|null $obj) {
            return (new TypeAny())->from($obj);
        };
    }

    public static function asDate($format = 'Y-m-d', Options $options = null)
    {
        return function (Type|null $obj) use ($format) {
            $type = (new TypeStringDate())->from($obj);
            (new TypeProxy($type))->setChildStateProperty('_extra.format', $format);

            return $type;
        };
    }

    public static function asEmail()
    {
        return function (Type|null $obj) {
            return (new TypeStringEmail())->from($obj);
        };
    }

    public static function asScalar()
    {
        return function (Type|null $obj) {
            return (new TypeScalar())->from($obj);
        };
    }

    public static function asFloat()
    {
        return function (Type|null $obj) {
            return (new TypeFloat())->from($obj);
        };
    }

    public static function asGroup()
    {
        return function (Type|null $obj) {
            return (new TypeGroup())->from($obj);
        };
    }

    public static function asInt()
    {
        return function (Type|null $obj) {
            return (new TypeInt())->from($obj);
        };
    }

    public static function asIntString()
    {
        return function (Type|null $obj) {
            return (new TypeStringInt())->from($obj);
        };
    }

    public static function asNumber()
    {
        return function (Type|null $obj) {
            return (new TypeNumber())->from($obj);
        };
    }

    public static function asString()
    {
        return function (Type|null $obj) {
            return (new TypeString())->from($obj);
        };
    }
}
