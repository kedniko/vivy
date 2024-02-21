<?php

namespace App;

use App\Types\TypeToken;
use Kedniko\Vivy\Core\Options;
use Kedniko\Vivy\Plugin\Standard\StandardLibrary;
use Kedniko\Vivy\Plugin\Standard\TypeString;
use Kedniko\Vivy\V;

class App
{
    public static function boot()
    {
        V::registerPlugin(new TypeToken());
        V::registerPlugin(new Rules());
        V::registerPlugin(new StandardLibrary());
    }
}
