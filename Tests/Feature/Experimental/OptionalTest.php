<?php

declare(strict_types=1);


namespace Tests;

use Kedniko\Vivy\V;

uses()->group('group');


test('optional-1', function () {
  $v = V::group([
    'name'    => V::optional()->int()->setValue('default name'),
    'surname' => V::optional()->string()->setValue('default surname'),
    'count'   => V::int()->setValue(1000),
  ]);

  $validated = $v->validate([
    'name'  => 3,
    'surname'  => '3',
    'count' => 340,
  ]);

  expect($validated->isValid())->toBeTrue();
  expect($validated->value())->toBe([
    'name'  => 'default name',
    'surname'  => 'default surname',
    'count' => 1000,
  ]);
});
