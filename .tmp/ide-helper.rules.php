<?php

// @formatter:off
/**
* A helper file for your Vivy validators.
*
* @author @kedniko
*/

namespace Kedniko\Vivy;

class V
{
	public static function token(Core\Options $options = null): \App\Types\TypeToken
	{
	}


	public static function phone(Core\Options $options = null): \App\Types\TypePhone
	{
	}


	public static function any(): Type
	{
	}


	/**
	 * @param  \Kedniko\Vivy\Type[]  $types
	 */
	public static function or(array $types, Core\Options $options = null): Plugins\StandardLibrary\TypeOr
	{
	}


	/**
	 * @param  array|callable  $setup
	 */
	public static function group(
		$setup = null,
		bool $stopOnFieldFailure = false,
		Core\Options $options = null,
	): Plugins\StandardLibrary\TypeGroup
	{
	}


	public static function file(Core\Options $options = null): Plugins\StandardLibrary\TypeFile
	{
	}


	public static function int(Core\Options $options = null): Plugins\StandardLibrary\TypeInt
	{
	}


	public static function bool(Core\Options $options = null): Plugins\StandardLibrary\TypeBool
	{
	}


	public static function float($strictFloat = false, Core\Options $options = null): Plugins\StandardLibrary\TypeFloat
	{
	}


	public static function number(Core\Options $options = null): Plugins\StandardLibrary\TypeNumber
	{
	}


	public static function null(Core\Options $options = null): Plugins\StandardLibrary\TypeNull
	{
	}


	public static function files(Core\Options $options = null): Plugins\StandardLibrary\TypeFiles
	{
	}


	public static function array(Core\Options $options = null): Plugins\StandardLibrary\TypeArray
	{
	}


	public static function string(Core\Options $options = null): Plugins\StandardLibrary\TypeString
	{
	}


	public static function date($format = 'Y-m-d', Core\Options $options = null): Plugins\StandardLibrary\TypeStringDate
	{
	}


	public static function intString(Core\Options $options = null): Plugins\StandardLibrary\TypeStringInt
	{
	}


	public static function boolString(Core\Options $options = null): Plugins\StandardLibrary\TypeStringBool
	{
	}


	public static function floatString(
		$strict = true,
		$trim = false,
		Core\Options $options = null,
	): Plugins\StandardLibrary\TypeStringFloat
	{
	}


	public static function numberString(Core\Options $options = null): Plugins\StandardLibrary\TypeStringNumber
	{
	}


	public static function email(Core\Options $options = null): Plugins\StandardLibrary\TypeStringEmail
	{
	}


	public static function emptyString(Core\Options $options = null): Plugins\StandardLibrary\TypeStringEmpty
	{
	}


	public static function notEmptyString(Core\Options $options = null): Plugins\StandardLibrary\TypeString
	{
	}


	public static function undefined(
		bool $enableDafault = false,
		mixed $value = null,
		Core\Options $options = null,
	): Plugins\StandardLibrary\TypeUndefined
	{
	}


	public static function asAny(): Plugins\StandardLibrary\TypeAny
	{
	}


	public static function asArray(): Plugins\StandardLibrary\TypeArray
	{
	}


	public static function asFile(): Plugins\StandardLibrary\TypeFile
	{
	}


	public static function asBool(): Plugins\StandardLibrary\TypeBool
	{
	}


	public static function asDate(
		$format = 'Y-m-d',
		Core\Options $options = null,
	): Plugins\StandardLibrary\TypeStringDate
	{
	}


	public static function asEmail(): Plugins\StandardLibrary\TypeStringEmail
	{
	}


	public static function asScalar(): Plugins\StandardLibrary\TypeScalar
	{
	}


	public static function asFloat(): Plugins\StandardLibrary\TypeFloat
	{
	}


	public static function asGroup(): Plugins\StandardLibrary\TypeGroup
	{
	}


	public static function asInt(): Plugins\StandardLibrary\TypeInt
	{
	}


	public static function asIntString(): Plugins\StandardLibrary\TypeStringInt
	{
	}


	public static function asNumber(): Plugins\StandardLibrary\TypeNumber
	{
	}


	public static function asString(): Plugins\StandardLibrary\TypeString
	{
	}
}

class Type
{
	/**
	 * @param  \Kedniko\Vivy\Type[]  $types
	 */
	public static function or(array $types, Core\Options $options = null): Plugins\StandardLibrary\TypeOr
	{
	}


	public static function asAny(): Plugins\StandardLibrary\TypeAny
	{
	}


	public static function asArray(): Plugins\StandardLibrary\TypeArray
	{
	}


	public static function asFile(): Plugins\StandardLibrary\TypeFile
	{
	}


	public static function asBool(): Plugins\StandardLibrary\TypeBool
	{
	}


	public static function asDate(
		$format = 'Y-m-d',
		Core\Options $options = null,
	): Plugins\StandardLibrary\TypeStringDate
	{
	}


	public static function asEmail(): Plugins\StandardLibrary\TypeStringEmail
	{
	}


	public static function asScalar(): Plugins\StandardLibrary\TypeScalar
	{
	}


	public static function asFloat(): Plugins\StandardLibrary\TypeFloat
	{
	}


	public static function asGroup(): Plugins\StandardLibrary\TypeGroup
	{
	}


	public static function asInt(): Plugins\StandardLibrary\TypeInt
	{
	}


	public static function asIntString(): Plugins\StandardLibrary\TypeStringInt
	{
	}


	public static function asNumber(): Plugins\StandardLibrary\TypeNumber
	{
	}


	public static function asString(): Plugins\StandardLibrary\TypeString
	{
	}
}


namespace App\Types;

class TypeAny
{
	public function token(\Kedniko\Vivy\Core\Options $options = null): TypeToken
	{
	}
}


namespace Kedniko\Vivy\Plugins\StandardLibrary;

class TypeAny
{
	/**
	 * @param  \Kedniko\Vivy\Type[]  $types
	 */
	public static function or(array $types, \Kedniko\Vivy\Core\Options $options = null): TypeOr
	{
	}


	/**
	 * @param  array|callable  $setup
	 */
	public static function group(
		$setup = null,
		bool $stopOnFieldFailure = false,
		\Kedniko\Vivy\Core\Options $options = null,
	): TypeGroup
	{
	}


	public static function file(\Kedniko\Vivy\Core\Options $options = null): TypeFile
	{
	}


	public static function int(\Kedniko\Vivy\Core\Options $options = null): TypeInt
	{
	}


	public static function bool(\Kedniko\Vivy\Core\Options $options = null): TypeBool
	{
	}


	public static function float($strictFloat = false, \Kedniko\Vivy\Core\Options $options = null): TypeFloat
	{
	}


	public static function number(\Kedniko\Vivy\Core\Options $options = null): TypeNumber
	{
	}


	public static function null(\Kedniko\Vivy\Core\Options $options = null): TypeNull
	{
	}


	public static function files(\Kedniko\Vivy\Core\Options $options = null): TypeFiles
	{
	}


	public static function array(\Kedniko\Vivy\Core\Options $options = null): TypeArray
	{
	}


	public static function string(\Kedniko\Vivy\Core\Options $options = null): TypeString
	{
	}


	public static function date($format = 'Y-m-d', \Kedniko\Vivy\Core\Options $options = null): TypeStringDate
	{
	}


	public static function intString(\Kedniko\Vivy\Core\Options $options = null): TypeStringInt
	{
	}


	public static function boolString(\Kedniko\Vivy\Core\Options $options = null): TypeStringBool
	{
	}


	public static function floatString(
		$strict = true,
		$trim = false,
		\Kedniko\Vivy\Core\Options $options = null,
	): TypeStringFloat
	{
	}


	public static function numberString(\Kedniko\Vivy\Core\Options $options = null): TypeStringNumber
	{
	}


	public static function email(\Kedniko\Vivy\Core\Options $options = null): TypeStringEmail
	{
	}


	public static function emptyString(\Kedniko\Vivy\Core\Options $options = null): TypeStringEmpty
	{
	}


	public static function notEmptyString(\Kedniko\Vivy\Core\Options $options = null): TypeString
	{
	}


	public static function undefined(
		bool $enableDafault = false,
		mixed $value = null,
		\Kedniko\Vivy\Core\Options $options = null,
	): TypeUndefined
	{
	}


	public static function asAny(): TypeAny
	{
	}


	public static function asArray(): TypeArray
	{
	}


	public static function asFile(): TypeFile
	{
	}


	public static function asBool(): TypeBool
	{
	}


	public static function asDate($format = 'Y-m-d', \Kedniko\Vivy\Core\Options $options = null): TypeStringDate
	{
	}


	public static function asEmail(): TypeStringEmail
	{
	}


	public static function asScalar(): TypeScalar
	{
	}


	public static function asFloat(): TypeFloat
	{
	}


	public static function asGroup(): TypeGroup
	{
	}


	public static function asInt(): TypeInt
	{
	}


	public static function asIntString(): TypeStringInt
	{
	}


	public static function asNumber(): TypeNumber
	{
	}


	public static function asString(): TypeString
	{
	}
}

class TypeString
{
	public static function date($format = 'Y-m-d', \Kedniko\Vivy\Core\Options $options = null): TypeStringDate
	{
	}


	public static function digits(\Kedniko\Vivy\Core\Options $options = null): TypeString
	{
	}
}
