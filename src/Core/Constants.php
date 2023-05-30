<?php

namespace Kedniko\Vivy\Core;

final class Constants
{
    public const REGEX_MAIL = '/^[A-Za-z0-9_]+([\-\+\.\'][A-Za-z0-9_]+)*@[A-Za-z0-9_]+([\-\.][A-Za-z0-9_]+)*\.[A-Za-z0-9_]+([\-\.][A-Za-z0-9_]+)*$/i';

    public const REGEX_CELLPHONE_WITH_OPTIONAL_PREFIX = '/^(\+[1-9]{1,3})?[0-9]{3,14}$/';

    public const REGEX_INTEGER_POSITIVE_OR_NEGATIVE = '/(^-?[1-9]\d+)$/';

    public const REGEX_INTEGER_POSITIVE = '/(^[1-9]\d+)$/';

    public const REGEX_FLOAT = '/(^[1-9]\d*\.\d+)$/';

    public const REGEX_FLOAT_POSITIVE_OR_NEGATIVE = '/^(-?[1-9]\d*\.\d+)$/';

    public const REGEX_DIGITS = '/^(\d+)$/';
}
