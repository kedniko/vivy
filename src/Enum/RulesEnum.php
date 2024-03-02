<?php

declare(strict_types=1);

namespace Kedniko\Vivy\Enum;

enum RulesEnum: string
{
  case ID_REQUIRED = 'required';
  case ID_NOT_EMPTY_STRING = 'notEmptyString';
  case ID_NOT_NULL = 'notNull';
  case ID_GROUP = 'group';
  case ID_EACH = 'each';
  case ID_OR = 'or';
  case ID_AND = 'and';
  case ID_NULL = 'null';
  case ID_EMPTY_STRING = 'emptyString';
  case ID_UNDEFINED = 'undefined';
}
