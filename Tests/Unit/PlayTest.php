<?php

namespace Tests;

use App\App;
use App\StringRule;

uses()->group('play');

beforeAll(function () {
    App::boot();
});

// test('play', function () {
//     StringRule::make();
// })->only();
