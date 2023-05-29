<?php

namespace Tests;

use App\App;
use Kedniko\Vivy\V;

uses()->group('bool');

beforeAll(function () {
    App::boot();
});

test('bool', function () {
    expect(V::bool()->validate(true)->isValid())->toBeTrue();
    expect(V::string()->bool()->validate('true')->isValid())->toBeTrue();

    expect(V::string()->bool()->toBool()->validate('true')->value())->toBe(true);
    expect(V::string()->bool()->tobool()->validate('false')->value())->toBe(false);
    expect(V::string()->bool()->tobool()->validate('error')->value())->toBe('error');

    expect(V::boolString()->tobool()->validate('true')->value())->toBe(true);
    expect(V::boolString()->tobool()->validate('false')->value())->toBe(false);
    expect(V::boolString()->tobool()->validate('error')->value())->toBe('error');

    expect(V::string()->bool()->validate('1')->isValid())->toBeFalse();
    expect(V::bool()->validate('10')->isValid())->toBeFalse();
});
