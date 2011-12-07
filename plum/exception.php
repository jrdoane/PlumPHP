<?php
namespace Plum;

// Basic exception wrapper.
class Exception extends \Exception {}
class LogicException extends Exception {}

// Primary exceptions
class ParameterException extends \Plum\Exception {
    public function __construct($param) {
        parent::__construct(var_export($param, true));
    }
}
class MissingParameterException extends \Plum\Exception {}

// Parameter sub-exceptions.
class ArrayExceptedException extends \Plum\ParameterException {}
class XmlNodeExpectedException extends \Plum\ParameterException {}
class HtmlNodeExpectedException extends \Plum\ParameterException {}

// XML sub exceptions
class XmlParseException extends \Plum\LogicException {}
class XmlBuildException extends \Plum\LogicException {}

