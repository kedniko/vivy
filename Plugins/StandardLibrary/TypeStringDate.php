<?php

namespace Kedniko\Vivy\Plugins\StandardLibrary;

use DateTime;
use Kedniko\Vivy\Contracts\ContextInterface;
use Kedniko\Vivy\Core\Helpers;
use Kedniko\Vivy\Core\Options;
use Kedniko\Vivy\Messages\RuleMessage;
use Kedniko\Vivy\Transformer;

final class TypeStringDate extends TypeString
{
    public function toFormat($format, Options $options = null)
    {
        $options = Options::build($options, func_get_args());
        $sourceFormat = $this->state->_extra['format'];
        $errormessage = $options->getErrorMessage() ?: RuleMessage::getErrorMessage();

        $transformer = new Transformer('toFormat', function (ContextInterface $c) use ($format, $sourceFormat): string {
            if ($c->value instanceof DateTime) {
                $given = $c->value;
            } else {
                $given = (new DateTime())->createFromFormat($sourceFormat, $c->value)->setTime(0, 0, 0, 0);
            }

            return $given->format($format);
        }, $errormessage);

        $this->addTransformer($transformer, $options);

        return $this;
    }

    public function minDate(DateTime|string $date, Options $options = null)
    {
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: RuleMessage::getErrorMessage('date.min');
        $sourceFormat = Helpers::issetOrFail($this->state->_extra['format']);
        if (is_string($date)) {
            $date = (new DateTime())->createFromFormat($sourceFormat, $date)->setTime(0, 0, 0, 0);
        }
        $this->addRule(Rules::minDate($date, $sourceFormat, $errormessage), $options);

        return $this;
    }

    public function maxDate(DateTime|string $date, Options $options = null)
    {
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: RuleMessage::getErrorMessage();
        $sourceFormat = Helpers::issetOrFail($this->state->_extra['format']);
        if (is_string($date)) {
            $date = (new DateTime())->createFromFormat($sourceFormat, $date)->setTime(0, 0, 0, 0);
        }
        $this->addRule(Rules::maxDate($date, $sourceFormat, $errormessage), $options);

        return $this;
    }

    public function between(DateTime|string $minDate, DateTime|string $maxDate, Options $options = null)
    {
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: RuleMessage::getErrorMessage('date.between');
        $sourceFormat = Helpers::issetOrFail($this->state->_extra['format']);
        if (is_string($minDate)) {
            $minDate = (new DateTime())->createFromFormat($sourceFormat, $minDate)->setTime(0, 0, 0, 0);
        }
        if (is_string($maxDate)) {
            $maxDate = (new DateTime())->createFromFormat($sourceFormat, $maxDate)->setTime(0, 0, 0, 0);
        }
        $this->addRule(Rules::between($minDate, $maxDate, $sourceFormat, $errormessage), $options);

        return $this;
    }

    public function notBetweenInclusive(DateTime $minDate, DateTime $maxDate, Options $options = null)
    {
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: RuleMessage::getErrorMessage('date.between');
        $sourceFormat = Helpers::issetOrFail($this->state->_extra['format']);
        $this->addRule(Rules::notBetween($minDate, $maxDate, $sourceFormat, $errormessage), $options);

        return $this;
    }

    // public function toIso($separator = '-', $errormessage = null)
    // {
    // 	$this->addTransformer(new Transformer('toIso', function(Context $c)use($separator){

    // 		$value = $c->value;

    // 		if(!$value || !is_string($value)){
    // 			return false;
    // 		}

    // 		// https://www.php.net/manual/en/regexp.reference.escape.php
    // 		$sepEscaped = preg_quote($this->separator, '/');

    // 		preg_match('/^([0-9]{2})'.$sepEscaped.'([0-9]{2})'.$sepEscaped.'([0-9]{4})$/', $value, $matches);

    // 		return "{$matches[3]}{$separator}{$matches[2]}{$separator}{$matches[1]}";

    // 	}), $errormessage);
    // 	return $this;
    // }
}
