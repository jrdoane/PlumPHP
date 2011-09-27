<?php
namespace Plum;

// Basic exception wrapper.
class Exception extends \Exception {}

// Primary exceptions
class ParameterException extends \Plum\Exception {
    public function __construct($param) {
        parent::__construct(var_export($param, true));
    }
}

// Parameter sub-exceptions.
class ArrayExceptedException extends \Plum\ParameterException {}
