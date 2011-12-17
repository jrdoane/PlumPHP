<?php
/**
 * Core PlumPHP library - Core exceptions
 *
 * PlumPHP is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PlumPHP is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PlumPHP.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace Plum;

// Basic exception wrapper.
class Exception extends \Exception {}
class LogicException extends Exception {}

// Primary exceptions
class ParameterException extends \Plum\Exception {
    public function __construct($param=null) {
        parent::__construct(var_export($param, true));
    }
}
class MissingParameterException extends \Plum\ParameterException {}
class InvalidParameterTypeException extends \Plum\ParameterException {}

// Parameter sub-exceptions.
class ArrayExceptedException extends \Plum\ParameterException {}
class XmlNodeExpectedException extends \Plum\ParameterException {}
class HtmlNodeExpectedException extends \Plum\XmlNodeExpectedException {}

// XML sub exceptions
class XmlParseException extends \Plum\LogicException {}
class XmlBuildException extends \Plum\LogicException {}

