<?php

namespace App;

use App\Types\TypeToken;
use Kedniko\Vivy\Context;
use Kedniko\Vivy\Core\Options;
use Kedniko\Vivy\Plugins\StandardLibrary\StandardLibrary;
use Kedniko\Vivy\Plugins\StandardLibrary\TypeString;
use Kedniko\Vivy\V;

class App
{
	public static function boot()
	{
		// V::register(V::BASE, 'name', function (string $name, Options $options = null) {
		// 	return V::rule('name', function (Context $c) use ($name) {
		// 		return $c->value === $name;
		// 	});
		// }, TypeString::class);
		V::registerPlugin(new TypeToken());
		V::registerPlugin(new Regole());
		V::registerPlugin(new StandardLibrary());
	}
}
