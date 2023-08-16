<?php

namespace Kedniko\Vivy\Contracts;

interface Context
{
  public function fatherContext();
  public function setFatherContext($fatherContext);
  public function rootContext();
  public function setRootContext($rootContext);
  public function setErrors($errors);
  public function args();
  public function isArrayContext();
  public function isGroupContext();
  public function setArgs($args);
  public function isRootContext();
  public function setIsRootContext($isRootContext);
  public function issetValue();
  public function unsetValue();
  public function isValid();
  public function getFieldContext(string $fieldname);
  // public function setValue($value);
  public function getField();
}
