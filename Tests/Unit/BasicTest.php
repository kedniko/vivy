<?php

namespace Tests;

use App\App;
use Kedniko\Vivy\V;

uses()->group('bool');

beforeAll(function () {
    App::boot();
});

test('true', function () {
    expect(true)->toBeTrue();
});
