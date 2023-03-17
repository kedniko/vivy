<?php

namespace Kedniko\Vivy\Core;

class Constants
{
	const REGEX_MAIL = '/^[A-Za-z0-9_]+([\-\+\.\'][A-Za-z0-9_]+)*@[A-Za-z0-9_]+([\-\.][A-Za-z0-9_]+)*\.[A-Za-z0-9_]+([\-\.][A-Za-z0-9_]+)*$/i';
	const REGEX_CELLPHONE_WITH_OPTIONAL_PREFIX = '/^(\+[1-9]{1,3})?[0-9]{3,14}$/';
	const REGEX_INTEGER_POSITIVE_OR_NEGATIVE = '/(^-?[1-9]\d+)$/';
	const REGEX_INTEGER_POSITIVE = '/(^[1-9]\d+)$/';
	const REGEX_FLOAT = '/(^[1-9]\d*\.\d+)$/';
	const REGEX_FLOAT_POSITIVE_OR_NEGATIVE = '/^(-?[1-9]\d*\.\d+)$/';
	const REGEX_DIGITS = '/^(\d+)$/';
}
