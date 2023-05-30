<?php

namespace Kedniko\Vivy\Support;

final class ColorCli
{
    public const C_DEFAULT = 'default';

    public const BLACK = 'black';

    public const RED = 'red';

    public const GREEN = 'green';

    public const YELLOW = 'yellow';

    public const BLUE = 'blue';

    public const PURPLE = 'purple';

    public const CYAN = 'cyan';

    public const WHITE = 'white';

    public static function color($value, $colorText = self::C_DEFAULT, $colorBack = self::C_DEFAULT)
    {
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

        $front = $frontArray[$front] ?? $frontArray[self::C_DEFAULT];
        $back = $backArray[$back] ?? $backArray[self::C_DEFAULT];

        return "\033[{$front};{$back}m";
    }
}
