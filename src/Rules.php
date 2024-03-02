<?php

declare(strict_types=1);

namespace Kedniko\Vivy;

use Kedniko\Vivy\Core\Rule;
use Kedniko\Vivy\Core\Helpers;
use Kedniko\Vivy\Enum\RulesEnum;
use Kedniko\Vivy\Messages\RuleMessage;
use Kedniko\Vivy\Contracts\ContextInterface;

class Rules
{
  public static function notNull(string|callable $errormessage = null): Rule
  {
    $ruleID = RulesEnum::ID_NOT_NULL->value;
    $ruleFn = function (ContextInterface $c): bool {
      return $c->value !== null;
    };

    $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.' . $ruleID);

    return new Rule($ruleID, $ruleFn, $errormessage);
  }

  public static function notEmptyString(string|callable $errormessage = null): Rule
  {
    $ruleID = RulesEnum::ID_NOT_EMPTY_STRING->value;
    $ruleFn = function (ContextInterface $c): bool {
      // $trim = Helpers::valueOrFunction($trim, $c);
      $value = $c->value;
      if (!is_string($value)) {
        return $c->value !== '';
      }
      // if (!$trim) {
      //     return $c->value !== '';
      // }
      $value = trim($value);

      return $c->value !== '';
    };
    $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.' . $ruleID);

    return new Rule($ruleID, $ruleFn, $errormessage);
  }


  public static function required(string|callable $errormessage = null): Rule
  {
    $ruleID = RulesEnum::ID_REQUIRED->value;
    $ruleFn = null;
    $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.' . $ruleID);

    return new Rule($ruleID, $ruleFn, $errormessage);
  }

  public static function equals(mixed $value, bool $strict, string|callable $errormessage = null): Rule
  {
    $ruleID = 'equals';
    $ruleFn = function (ContextInterface $c) use ($value, $strict): bool {
      $value = Helpers::valueOrFunction($value, $c);
      $strict = Helpers::valueOrFunction($strict, $c);

      return $strict === true ? $c->value === $value : $c->value == $value;
    };

    $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.' . $ruleID);

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

    $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.' . $ruleID);

    return new Rule($ruleID, $ruleFn, $errormessage);
  }


  public static function null(string|callable $errormessage = null): Rule
  {
    $ruleID = RulesEnum::ID_NULL->value;
    $ruleFn = function (ContextInterface $c): bool {
      return $c->value === null;
    };

    $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.' . $ruleID);

    return new Rule($ruleID, $ruleFn, $errormessage);
  }



  public static function emptyString(string|callable $errormessage = null): Rule
  {
    $ruleID = RulesEnum::ID_EMPTY_STRING->value;
    $ruleFn = function (ContextInterface $c): bool {
      // $trim = Helpers::valueOrFunction($trim, $c);
      $value = $c->value;
      if (!is_string($value)) {
        return $c->value === '';
      }
      // if (!$trim) {
      //     return $c->value === '';
      // }
      $value = trim($value);

      return $c->value === '';
    };
    $errormessage = $errormessage ?: RuleMessage::getErrorMessage('default.' . $ruleID);

    return new Rule($ruleID, $ruleFn, $errormessage);
  }
}
