<?php

declare(strict_types=1);

namespace Kedniko\Vivy;

use Kedniko\Vivy\Contracts\MiddlewareInterface;
use Kedniko\Vivy\Core\LinkedList;
use Kedniko\Vivy\Core\Middleware;
use Kedniko\Vivy\Contracts\TypeInterface;

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
      $serialized['rules'][$middleware->getOptions()->getFunctionName()] = $middleware->getArgs();
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
