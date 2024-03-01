<?php

declare(strict_types=1);

namespace Tests;

use Kedniko\Vivy\Support\Util;

uses()->group('bsae');

test('is-ordered-indexed-array', function () {
  expect(Util::isOrderedIndexedArray(['a', 'b', 'c']))->toBeTrue();
  expect(Util::isOrderedIndexedArray([2, 3, 4, 5, 6]))->toBeTrue();
  expect(Util::isOrderedIndexedArray(['a', 'b', '' => 'c']))->toBeFalse();
  expect(Util::isOrderedIndexedArray(['a', 'b', 'key' => 'c']))->toBeFalse();
  expect(Util::isOrderedIndexedArray(['a', 'b', null => 'c']))->toBeFalse();
});
