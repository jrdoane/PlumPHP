<?php
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
