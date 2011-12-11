<?php
/**
 * Core PlumPHP Library - HTML (XML Extension)
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

/**
 * This class builds the html itself syntactically.
 */
class Html extends Xml {
    private static $html_type;
    /**
     * Legacy HTML will not be supported. PlumPHP will aim to use the latest 
     * software at the time the function was written.
     */
    public static function doctype($type='html5') {
        switch($type) {
        case 'html5':
            return "<!DOCTYPE HTML>";
        default:
            throw new Exception("Unsupported doctype.");
        }
    }


    public static function builder($name = 'html', $attr = array(), $value = '') {
        return new HtmlBuilder($name, $attr, $value);
    } 
}

/**
 * This class builds html structure-wise.
 */
class HtmlBuilder extends XmlBuilder{
    public function __construct($name = 'html', $attr = array(), $value = '') {
        parent::__construct($name, $attr, $value);
    }

    public function p($val, $attr = array()) {
        $this->tag('p', $attr, $val);
    }

    public function pre($val, $attr = array()) {
        $this->tag('pre', $attr, $val);
    }
}

// Add any Html tag specific things here.
class HtmlNode extends XmlNode { }
