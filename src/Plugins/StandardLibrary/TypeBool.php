<?php

namespace Kedniko\Vivy\Plugins\StandardLibrary;

use Kedniko\Vivy\Context;
use Kedniko\Vivy\Core\Helpers;
use Kedniko\Vivy\Core\Options;
use Kedniko\Vivy\Core\Rule;
use Kedniko\Vivy\Core\Transformers;
use Kedniko\Vivy\Messages\RuleMessage;
use Kedniko\Vivy\Messages\TransformerMessage;
use Kedniko\Vivy\Rules;

final class TypeBool extends TypeScalar
{
    public function equals($bool, $strict = true, Options $options = null)
    {
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: RuleMessage::getErrorMessage('bool.is');
        $this->addRule(self::ruleBooeanIs($bool, $errormessage), $options);

        return $this;
    }

    public function isTrue(Options $options = null)
    {
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: RuleMessage::getErrorMessage('bool.isTrue');
        $rule = new Rule('bool-is-true', fn (Context $c): bool => $c->value === true, $errormessage);

        $this->addRule($rule, $options);

        return $this;
    }

    public function isFalse(Options $options = null)
    {
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: RuleMessage::getErrorMessage('bool.isFalse');
        $rule = new Rule('bool-is-false', fn (Context $c): bool => $c->value === false, $errormessage);

        $this->addRule($rule, $options);

        return $this;
    }

    // Rules

    private static function ruleBooeanIs($bool, $errormessage = null): Rule
    {
        $ruleID = 'boolIs';
        $ruleFn = fn (Context $c): bool => $c->value === $bool;

        $errormessage = $errormessage ?: RuleMessage::getErrorMessage("default.{$ruleID}");

        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    public function toInteger(Options $options = null)
    {
        $options = Helpers::getOptions($options);
        $errormessage = $options->getErrorMessage() ?: TransformerMessage::getErrorMessage('boolToInt');

        $ruleID = Transformers::ID_BOOL_TO_INT;
        $type = (new TypeInt())->from($this);
        $type->required($errormessage ?: RuleMessage::getErrorMessage("{$ruleID}.required"));
        $type->addRule(Rules::notNull($errormessage ?: RuleMessage::getErrorMessage("{$ruleID}.notNull")));
        $type->addRule(Rules::notEmptyString($errormessage ?: RuleMessage::getErrorMessage("{$ruleID}.notEmptyString")));
        $type->addRule(Rules::int($errormessage ?: RuleMessage::getErrorMessage("{$ruleID}.type")), $options);
        $type->addTransformer(Transformers::boolToInt($errormessage), $options);

        return $type;
    }

    public function toString(Options $options = null)
    {
        $options = Helpers::getOptions($options);
        $errormessage = $options->getErrorMessage() ?: TransformerMessage::getErrorMessage('boolToInt');

        $ruleID = Transformers::ID_BOOL_TO_STRING;
        $type = (new TypeString())->from($this);
        $type->required($errormessage ?: RuleMessage::getErrorMessage("{$ruleID}.required"));
        $type->addRule(Rules::notNull($errormessage ?: RuleMessage::getErrorMessage("{$ruleID}.notNull")));
        $type->addRule(Rules::notEmptyString($errormessage ?: RuleMessage::getErrorMessage("{$ruleID}.notEmptyString")));
        $type->addRule(Rules::bool($errormessage ?: RuleMessage::getErrorMessage("{$ruleID}.type")), $options);
        $type->addTransformer(Transformers::boolToString($errormessage), $options);

        return $type;
    }
}
