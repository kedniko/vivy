<?php

namespace Kedniko\Vivy\Plugins\StandardLibrary;

use Kedniko\Vivy\Contracts\ContextInterface;
use Kedniko\Vivy\Core\Helpers;
use Kedniko\Vivy\Core\Options;
use Kedniko\Vivy\Core\Rule;
use Kedniko\Vivy\Messages\RuleMessage;
use Kedniko\Vivy\Messages\TransformerMessage;
use Kedniko\Vivy\Transformer;

class TypeString extends TypeScalar
{
    public function prefix($string, Options $options = null)
    {
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: TransformerMessage::getErrorMessage('string.prefix');
        $transformer = new Transformer('prefix', function (ContextInterface $c): string {
            $prefix = Helpers::issetOrDefault($c->args()[0], '');

            return $prefix.$c->value;
        }, $errormessage);

        $this->addTransformer($transformer, $options);

        return $this;
    }

    public function trim($characters = " \t\n\r\0\x0B", Options $options = null)
    {
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: TransformerMessage::getErrorMessage('trim');
        $this->addTransformer(Transformers::trim($characters, $errormessage), $options);

        return $this;
    }

    public function ltrim($characters, Options $options = null)
    {
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: TransformerMessage::getErrorMessage('ltrim');
        $this->addTransformer(Transformers::ltrim($characters, $errormessage), $options);

        return $this;
    }

    public function rtrim($characters, Options $options = null)
    {
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: TransformerMessage::getErrorMessage('rtrim');
        $this->addTransformer(Transformers::rtrim($characters, $errormessage), $options);

        return $this;
    }

    public function toUppercase(Options $options = null)
    {
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: TransformerMessage::getErrorMessage('toUpperCase');
        $this->addTransformer(Transformers::toUpperCase($errormessage), $options);

        return $this;
    }

    public function toFirstLetterUpperCase(Options $options = null)
    {
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: TransformerMessage::getErrorMessage('firstLetterUpperCase');
        $this->addTransformer(Transformers::firstLetterUpperCase($errormessage), $options);

        return $this;
    }

    public function toLowercase(Options $options = null)
    {
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: TransformerMessage::getErrorMessage('toLowerCase');
        $this->addTransformer(Transformers::toLowerCase($errormessage), $options);

        return $this;
    }

    public function toFirstLetterLowerCase(Options $options = null)
    {
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: TransformerMessage::getErrorMessage('firstLetterLowerCase');
        $this->addTransformer(Transformers::firstLetterLowerCase($errormessage), $options);

        return $this;
    }

    public function startsWith(string $startsWith, $ignoreCase = true, Options $options = null)
    {
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: RuleMessage::getErrorMessage('string.startsWith');
        $this->addRule(Rules::ruleStartsWith($startsWith, $ignoreCase = true, $errormessage), $options);

        return $this;
    }

    public function endsWith(string $endsWith, $ignoreCase = true, Options $options = null)
    {
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: RuleMessage::getErrorMessage('string.endsWith');
        $this->addRule(Rules::ruleEndsWith($endsWith, $ignoreCase = true, $errormessage), $options);

        return $this;
    }

    public function contains(string $contains, $ignoreCase = true, Options $options = null)
    {
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: RuleMessage::getErrorMessage('string.contains');
        $this->addRule(Rules::ruleContains($contains, $ignoreCase = true, $errormessage), $options);

        return $this;
    }

    public function length(int $length, Options $options = null)
    {
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: RuleMessage::getErrorMessage('string.length');
        $this->addRule(Rules::ruleLength($length, $errormessage), $options);

        return $this;
    }

    public function minLength($length, Options $options = null)
    {
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: RuleMessage::getErrorMessage('string.minLength');
        $this->addRule(Rules::ruleMinLength($length, $errormessage), $options);

        return $this;
    }

    public function maxLength($length, Options $options = null)
    {
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: RuleMessage::getErrorMessage('string.maxLength');
        $this->addRule(Rules::ruleMaxLength($length, $errormessage), $options);

        return $this;
    }

    public function intString(Options $options = null)
    {
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: RuleMessage::getErrorMessage('intString.type');
        $this->addRule(Rules::intString($errormessage), $options);

        return $this;
    }

    public function bool(Options $options = null)
    {
        $options = Options::build($options, func_get_args());

        $errormessage = $options->getErrorMessage() ?: RuleMessage::getErrorMessage('boolString.type');
        $this->addRule(Rules::boolString($errormessage), $options);

        return (new TypeStringBool())->from($this);
    }

    // Rules

    // private static function ruleIntString($errormessage = null)
    // {
    //     $ruleID = Rules::ID_INT_STRING;
    //     $ruleFn = function (Context $c) {
    //         if (!is_string($c->value)) {
    //             return false;
    //         }

    //         $value = trim(strval($c->value));

    //         // accept negative integers
    //         $shouldRemoveFirstCharacter = substr($value, 0, 1) === '-';
    //         if ($shouldRemoveFirstCharacter) {
    //             $value = substr($value, 1);
    //         }

    //         $isTypeIntString = ctype_digit($value);
    //         return $isTypeIntString;
    //     };

    //     $errormessage = $errormessage ?: RuleMessage::getErrorMessage("default.{$ruleID}");

    //     return new Rule($ruleID, $ruleFn, $errormessage);
    // }
}
