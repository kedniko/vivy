<?php

namespace App\Types;

use Kedniko\Vivy\Context;
use Kedniko\Vivy\Core\Options;
use Kedniko\Vivy\Plugins\StandardLibrary\TypeString;
use Kedniko\Vivy\Support\Str;
use Kedniko\Vivy\V;

class TypePhone extends TypeString
{
	public static function phone(Options $options = null)
	{
		$type = new TypePhone();
		$type->addRule(V::rule('phone', function (Context $c) {
			return is_string($c->value);
		}), $options);
		return $type;
	}

	public function toPL(Options $options = null)
	{
		$this->addTransformer(V::transformer('toPL', function (Context $c) {
			return '+48 ' . $c->value;
		}), $options);
		return $this;
	}

	public function toIT(Options $options = null)
	{
		$this->addTransformer(V::transformer('toIT', function (Context $c) {
			return '+39 ' . $c->value;
		}), $options);
		return $this;
	}

	public function startsWith123(Options $options = null)
	{
		$this->addRule(V::rule('startsWith123', function (Context $c) {
			return Str::startsWith($c->value, '123');
		}), $options);
		return $this;
	}

	public function toUS(Options $options = null)
	{
		$this->addTransformer(V::transformer('toUS', function (Context $c) {
			return '+1 ' . $c->value;
		}), $options);
		return $this;
	}
}
