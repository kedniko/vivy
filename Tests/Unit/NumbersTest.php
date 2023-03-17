<?php

namespace Tests;

use App\App;
use Kedniko\Vivy\ArrayContext;
use Kedniko\Vivy\Core\GroupContext;
use Kedniko\Vivy\V;

uses()->group('numbers');

beforeAll(function () {
	App::boot();
});

test('number-int', function () {
	expect(V::int()->validate(10)->isValid())->toBeTrue();
	expect(V::int()->validate('10')->isValid())->toBeFalse();
});

test('number-float-int', function () {
	expect(V::float()->validate(10)->isValid())->toBeTrue();
	expect(V::float()->validate('10')->isValid())->toBeFalse();
});

test('number-number', function () {
	expect(V::number()->validate(10)->isValid())->toBeTrue();
	expect(V::number()->validate(10.0043)->isValid())->toBeTrue();
	expect(V::number()->validate('10')->isValid())->toBeFalse();
	expect(V::number()->validate('10.0043')->isValid())->toBeFalse();
});

test('number-number-decimals', function () {
	expect(V::number()->fractionalPartMax(0.0050)->validate(10.0043)->isValid())->toBeTrue();
	expect(V::number()->fractionalPartMax(0.0050)->validate(10.0053)->isValid())->toBeFalse();
	expect(V::number()->fractionalPartMax('0.0050')->validate(10.0043)->isValid())->toBeTrue();
	expect(V::number()->fractionalPartMax('0.0050')->validate(10.0053)->isValid())->toBeFalse();

	expect(V::number()->decimalPartMin(0.0050)->validate(10.0053)->isValid())->toBeTrue();
	expect(V::number()->decimalPartMin(0.0050)->validate(10.0043)->isValid())->toBeFalse();
	expect(V::number()->decimalPartMin('0.0050')->validate(10.0053)->isValid())->toBeTrue();
	expect(V::number()->decimalPartMin('0.0050')->validate(10.0043)->isValid())->toBeFalse();

	expect(V::number()->decimalPartIs(0.0050)->validate(10.0050)->isValid())->toBeTrue();
	expect(V::number()->decimalPartIs(0.0050)->validate(10.0053)->isValid())->toBeFalse();
	expect(V::number()->decimalPartIs('0.0050')->validate(10.0050)->isValid())->toBeTrue();
	expect(V::number()->decimalPartIs('0.0050')->validate(10.0053)->isValid())->toBeFalse();

	expect(V::number()->multipleOf(0.25)->validate(10.50)->isValid())->toBeTrue();
	expect(V::number()->multipleOf(0.25)->validate(10.0053)->isValid())->toBeFalse();
	expect(V::number()->multipleOf('0.25')->validate(10.50)->isValid())->toBeTrue();
	expect(V::number()->multipleOf('0.25')->validate(10.0053)->isValid())->toBeFalse();
});

test('number-float', function () {
	expect(V::float()->validate(10.0023)->isValid())->toBeTrue();
	expect(V::float()->validate('10.0023')->isValid())->toBeFalse();
});

test('number-int-string', function () {
	expect(V::intString()->validate('10')->isValid())->toBeTrue();
	expect(V::intString()->validate(10)->isValid())->toBeFalse();
});

test('number-float-int-string', function () {
	expect(V::floatString(false)->validate('10')->isValid())->toBeTrue();
	expect(V::floatString()->validate('10')->isValid())->toBeFalse();
	expect(V::floatString()->validate(10)->isValid())->toBeFalse();
});

test('number-number-string', function () {
	expect(V::numberString()->validate('10')->isValid())->toBeTrue();
	expect(V::numberString()->validate('10.0043')->isValid())->toBeTrue();
	expect(V::numberString()->validate(10)->isValid())->toBeFalse();
	expect(V::numberString()->validate(10.0043)->isValid())->toBeFalse();
});
test('number-float-string', function () {
	expect(V::floatString()->validate('10.0023')->isValid())->toBeTrue();
	expect(V::floatString()->validate(10.0023)->isValid())->toBeFalse();
});
test('string-digits', function () {
	expect(V::string()->digits()->validate('0032432047328')->isValid())->toBeTrue();
	expect(V::string()->digits()->validate('3312345678900032432047328')->isValid())->toBeTrue();
	expect(V::string()->digits()->validate('00324320.47328')->isValid())->toBeFalse();
	expect(V::string()->digits()->validate('32432047328e5')->isValid())->toBeFalse();
});

test('setValue', function () {
	expect(V::int()->setValue(fn ($c) => $c->value / 100)->validate(10)->value())->toBe(10 / 100);
});