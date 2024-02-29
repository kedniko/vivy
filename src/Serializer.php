<?php

declare(strict_types=1);

namespace Kedniko\Vivy;

use Kedniko\Vivy\Contracts\MiddlewareInterface;
use Kedniko\Vivy\Core\LinkedList;
use Kedniko\Vivy\Core\Middleware;
use Kedniko\Vivy\Contracts\TypeInterface;
use Kedniko\Vivy\Core\Rule;

class Serializer
{
  public function __construct()
  {
    //
  }

  public function encode(TypeInterface $type)
  {

    $linkedList = $type->state->getMiddlewares();
    assert($linkedList instanceof LinkedList);
    $linkedList->rewind();
    $serialized = [];

    while ($linkedList->hasNext()) {

      $middleware = $linkedList->getNext();
      assert($middleware instanceof MiddlewareInterface);
      $name = $middleware->getOptions()->getFunctionName();
      $serialized['rules'][$name] = $middleware->getArgs();

      if ($middleware instanceof Rule && $name === 'group') {
        $setup = $middleware->getArgs()[0];
        foreach ($setup as $key => $value) {
          if (is_array($value)) {
            $value = V::group($value);
          }
          $serialized['rules'][$name][0][$key] = $this->encode($value);
        }
      } else if ($middleware instanceof Rule && $name === 'each') {
        $value = $middleware->getArgs()[0];
        if (is_array($value)) {
          $value = V::group($value);
        }
        $serialized['rules'][$name][0] = $this->encode($value);
      } else if ($middleware instanceof Rule && $name === 'or') {
        $setup = $middleware->getArgs()[0];
        foreach ($setup as $key => $value) {
          if (is_array($value)) {
            $value = V::group($value);
          }
          $serialized['rules'][$name][0][$key] = $this->encode($value);
        }
      }
    }

    return $serialized;
  }

  public function decode(array $json): TypeInterface|null
  {
    $type = null;
    $caller = V::$magicCaller;
    foreach ($json['rules'] as $ruleId => $args) {
      $type = $type ? $type->$ruleId(...$args) : V::$ruleId(...$args);
    }
    return $type;
  }
}
