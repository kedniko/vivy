<?php

namespace Kedniko\Vivy;

use Kedniko\Vivy\Core\Options;

class O
{
    public static function options($options = [])
    {
        if (is_array($options)) {
            $o = new Options();
            if (isset($options['message'])) {
                $o->message($options['message']);
            }
            if (isset($options['stopOnFailure'])) {
                $o->stopOnFailure($options['stopOnFailure']);
            }
            $options = $o;
        }

        $options = Options::build($options, func_get_args());
        $o = new Options();
        $o->message($options->getErrorMessage());
        $o->stopOnFailure($options->getStopOnFailure());

        return $o;
    }

    public static function continueOnFailure()
    {
        return self::options()->continueOnFailure();
    }

    public static function stopOnFailure()
    {
        return self::options()->stopOnFailure();
    }

    public static function message($message)
    {
        return self::options()->message($message);
    }

    public static function once()
    {
        return self::options()->once();
    }

    public static function appendAfterCurrent()
    {
        return self::options()->appendAfterCurrent();
    }

    public static function ifRule($if)
    {
        return self::options()->ifRule($if);
    }

    public static function ifArrayIndex($index)
    {
        return self::options()->ifRule(function (ArrayContext $ac) use ($index) {
            return $ac->getIndex() === $index;
        });
    }
}
