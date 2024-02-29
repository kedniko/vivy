<?php

namespace Tests;

use Kedniko\Vivy\O;
use Kedniko\Vivy\V;

uses()->group('performance');


// test('performance-native', function () {
//     if (function_exists('xdebug_is_debugger_active')) {
//         $isDebugging = call_user_func('xdebug_is_debugger_active');
//         if ($isDebugging) {
//             expect(true)->toBe(false, 'Xdebug is active, please disable it for performance tests');

//             return;
//         }
//     }

//     $hrtime_start = hrtime(true);
//     $r = range(1, 50000);
//     $post = [
//         'num' => $r,
//     ];
//     $l = count($r);
//     for ($i = 1; $i < $l; $i++) {
//         is_int($post['num'][$i]);
//     }
//     $ms = (hrtime(true) - $hrtime_start) / 1e+6;
//     echo ('performance-native: ' . $ms) . ' ms' . PHP_EOL;
//     expect($ms)->toBeLessThan(30);
// })->skip();

// test('test-1', function () {
//     if (function_exists('xdebug_is_debugger_active')) {
//         $isDebugging = call_user_func('xdebug_is_debugger_active');
//         if ($isDebugging) {
//             expect(true)->toBe(false, 'Xdebug is active, please disable it for performance tests');

//             return;
//         }
//     }

//     $hrtime_start = hrtime(true);
//     $r = range(1, 50000);
//     $post = [
//         'num' => $r,
//     ];
//     $v = V::group(['num' => V::array()->each(V::int())]);
//     $validated = $v->validate($post);
//     expect($validated->isValid())->toBeTrue();
//     $ms = (hrtime(true) - $hrtime_start) / 1e+6;
//     echo ('test-1: ' . $ms . ' ms') . PHP_EOL;
//     expect($ms)->toBeLessThan(3000);
// })->skip();


// test('small-optimization', function () {
//     $r = range(1, 50000);
//     $post = [
//         'num' => $r,
//     ];
//     $v = V::group([
//         'num' => V::array()->each(
//             V::int(O::options()->ignoreContext())
//         ),
//     ]);

//     $hrtime_start = hrtime(true);

//     $validated = $v->validate($post);
//     expect($validated->isValid())->toBeTrue();

//     $ms = (hrtime(true) - $hrtime_start) / 1e+6;
//     echo ('small-optimization: ' . $ms . ' ms') . PHP_EOL;
//     expect($ms)->toBeLessThan(3000);
// })->skip();



// test('performance-with-functions', function () {
//     // $isInt = function ($value) {
//     //     return is_int($value);
//     // };

//     $class = \Kedniko\Vivy\Rules\RuleFunctions::class;
//     $method = 'int';

//     $r = range(1, 50000);
//     $post = [
//         'num' => $r,
//     ];
//     $l = count($r);
//     $c = new Context();
//     for ($i = 1; $i < $l; $i++) {
//         for ($j = 0; $j < 300; $j++) {
//             $a = 1;
//         }
//         $c->value = $post['num'][$i];
//         $class::$method($c);

//         // call static string function in php 5
//     }
// })->only();