<?php

namespace Kedniko\Vivy\Contracts;

interface MiddlewareInterface
{

  public function getID();
  public function getCallback();
  public function getErrorMessage();
  public function setErrorMessage($errmessage);
  public function setStopOnFailure($stopOnFailure);
  public function getStopOnFailure();
  public function getArgs();
  public function isRule();
  public function setArgs($args);
  public function getOptions();
  public function setOptions($options);
}
