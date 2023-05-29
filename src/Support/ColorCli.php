<?php

namespace Kedniko\Vivy\Support;

class ColorCli
{
    const C_DEFAULT = 'default';

    const BLACK = 'black';

    const RED = 'red';

    const GREEN = 'green';

    const YELLOW = 'yellow';

    const BLUE = 'blue';

    const PURPLE = 'purple';

    const CYAN = 'cyan';

    const WHITE = 'white';

    public static function color($value, $colorText = self::C_DEFAULT, $colorBack = self::C_DEFAULT)
    {
        if (version_compare(PHP_VERSION, '7.0.0', '<')) {
            return $value;
        }
        $color = self::getColorString($colorText, $colorBack);

        return "{$color}{$value}\033[0m";
    }

    private static function getColorString($front = self::C_DEFAULT, $back = self::C_DEFAULT)
    {
        $frontArray = [
            self::C_DEFAULT => '39',
            self::BLACK => '30',
            self::RED => '31',
            self::GREEN => '32',
            self::YELLOW => '33',
            self::BLUE => '34',
            self::PURPLE => '35',
            self::CYAN => '36',
            self::WHITE => '37',
        ];
        $backArray = [
            self::C_DEFAULT => '49',
            self::BLACK => '40',
            self::RED => '41',
            self::GREEN => '42',
            self::YELLOW => '43',
            self::BLUE => '44',
            self::PURPLE => '45',
            self::CYAN => '46',
            self::WHITE => '47',
        ];

        $front = isset($frontArray[$front]) ? $frontArray[$front] : $frontArray[self::C_DEFAULT];
        $back = isset($backArray[$back]) ? $backArray[$back] : $backArray[self::C_DEFAULT];

        return "\033[{$front};{$back}m";
    }
}
