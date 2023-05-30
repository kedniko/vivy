<?php

namespace Kedniko\Vivy\Plugins\StandardLibrary;

use Brick\Math\BigDecimal;
use Kedniko\Vivy\Context;
use Kedniko\Vivy\Core\Options;
use Kedniko\Vivy\Core\Rule;
use Kedniko\Vivy\Core\Transformers;
use Kedniko\Vivy\Messages\RuleMessage;
use Kedniko\Vivy\Rules;
use Kedniko\Vivy\V;

class TypeNumber extends TypeScalar
{
    public const ID_MAX = 'numberMax';

    public const ID_MIN = 'numberMin';

    public const ID_NUMBER_DECIMAL_PART_MAX = 'numberDecimalPartMax';

    public const ID_NUMBER_DECIMAL_PART_MIN = 'numberDecimalPartMin';

    public const ID_NUMBER_DECIMAL_PART_IS = 'numberDecimalPartIs';

    public const ID_NUMBER_MULTIPLE_OF = 'numberMultipleOf';

    public const ID_NUMBER_BETWEEN = 'numberBetween';

    public const ID_NUMBER_NOT_BETWEEN = 'numberNotBetween';

    public const ID_NUMBER_TO_STRING = 'numberToString';

    public function min(\Brick\Math\BigNumber|int|float|string $min, Options $options = null)
    {
        $ruleid = self::ID_MIN;
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: RuleMessage::getErrorMessage("number.{$ruleid}");
        $this->addRule(Rules::min($min, $errormessage), $options);

        return $this;
    }

    public function max(\Brick\Math\BigNumber|int|float|string $max, Options $options = null)
    {
        $ruleid = self::ID_MAX;
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: RuleMessage::getErrorMessage("number.{$ruleid}");
        $this->addRule(Rules::max($max, $errormessage), $options);

        return $this;
    }

    public function decimalPartIs(\Brick\Math\BigNumber|int|float|string $number, Options $options = null)
    {
        $ruleID = self::ID_NUMBER_DECIMAL_PART_IS;
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: RuleMessage::getErrorMessage("number.{$ruleID}");

        $rule = V::rule($ruleID, function (Context $c) use ($number): bool {
            try {
                $srcFractionalPart = BigDecimal::of('0.'.BigDecimal::of($c->value)->getFractionalPart());
                $destfractionalPart = BigDecimal::of('0.'.BigDecimal::of($number)->getFractionalPart());

                return $srcFractionalPart->isEqualTo($destfractionalPart);
            } catch (\Throwable) {
                return false;
            }
        }, $errormessage);

        $this->addRule($rule, $options);

        return $this;
    }

    public function multipleOf(\Brick\Math\BigNumber|int|float|string $number, Options $options = null)
    {
        $ruleID = self::ID_NUMBER_MULTIPLE_OF;
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: RuleMessage::getErrorMessage("number.{$ruleID}");

        $rule = V::rule($ruleID, function (Context $c) use ($number): bool {
            try {
                $result = BigDecimal::of($c->value)->remainder($number);

                return $result->isEqualTo(BigDecimal::of(0));
            } catch (\Throwable) {
                return false;
            }
        }, $errormessage);

        $this->addRule($rule, $options);

        return $this;
    }

    public function decimalPartMin(\Brick\Math\BigNumber|int|float|string $min, Options $options = null)
    {
        $ruleID = self::ID_NUMBER_DECIMAL_PART_MIN;
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: RuleMessage::getErrorMessage("number.{$ruleID}");

        $rule = V::rule($ruleID, function (Context $c) use ($min): bool {
            try {
                $srcFractionalPart = BigDecimal::of($c->value)->getFractionalPart();
                $destfractionalPart = BigDecimal::of($min)->getFractionalPart();
                $src = BigDecimal::of("0.$srcFractionalPart");
                $dest = BigDecimal::of("0.$destfractionalPart");

                return $src->isGreaterThanOrEqualTo($dest);
            } catch (\Throwable) {
                return false;
            }
        }, $errormessage);

        $this->addRule($rule, $options);

        return $this;
    }

    public function fractionalPartMax(\Brick\Math\BigNumber|int|float|string $max, Options $options = null)
    {
        $ruleID = self::ID_NUMBER_DECIMAL_PART_MAX;
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: RuleMessage::getErrorMessage("number.{$ruleID}");

        $rule = V::rule($ruleID, function (Context $c) use ($max): bool {
            try {
                $srcFractionalPart = BigDecimal::of($c->value)->getFractionalPart();
                $destfractionalPart = BigDecimal::of($max)->getFractionalPart();
                $src = BigDecimal::of("0.$srcFractionalPart");
                $dest = BigDecimal::of("0.$destfractionalPart");

                return $src->isLessThanOrEqualTo($dest);
            } catch (\Throwable) {
                return false;
            }
        }, $errormessage);

        $this->addRule($rule, $options);

        return $this;
    }

    public function between($min, $max, Options $options = null)
    {
        $ruleid = self::ID_NUMBER_BETWEEN;
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: RuleMessage::getErrorMessage("number.{$ruleid}");
        $rule = new Rule($ruleid, function (Context $c) use ($min, $max): bool {
            $value = $c->value;

            return $value >= $min && $value <= $max;
        }, $errormessage);

        $this->addRule($rule, $options);

        return $this;
    }

    public function notBetween($min, $max, Options $options = null)
    {
        $ruleid = self::ID_NUMBER_NOT_BETWEEN;
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: RuleMessage::getErrorMessage("number.{$ruleid}");
        $rule = new Rule($ruleid, function (Context $c) use ($min, $max): bool {
            $value = $c->value;

            return ! ($value >= $min && $value <= $max);
        }, $errormessage);

        $this->addRule($rule, $options);

        return $this;
    }

    public function toString($errormessage = null, Options $options = null)
    {
        $ruleid = self::ID_NUMBER_TO_STRING;
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: RuleMessage::getErrorMessage("number.{$ruleid}");

        $transformer = Transformers::numberToString($errormessage);
        $this->addTransformer($transformer);

        return (new TypeString())->from($this);
    }
}
