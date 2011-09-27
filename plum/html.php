<?php
namespace Plum;

/**
 * This class builds the html itself syntactically.
 */
class Html {
    private static $html_type;
    /**
     * Legacy HTML will not be supported. PlumPHP will aim to use the latest 
     * software at the time the function was written.
     */
    public static function doctype($type='html5') {
        switch($type) {
        case 'html5':
            self::$html_type = $type;
            break;
        default:
            throw new Exception("Unsupported doctype.");
        }
    }

    public static function tag($name, $attributes, $value) {
        $out = "<$name";
        foreach($attributes as $n => $v) {
            $out .= " {$n}=\"{$v}\"";
        }
        if(empty($value)) {
            $out .= " />\n";
            return $out;
        }
        $out .= ">{$value}</$name>";
        return $out;
    }

    public static function builder($name = 'html', $attr = array(), $value = '') {
        return new HtmlBuilder($name, $attr, $value);
    } 
}

/**
 * This class builds html structure-wise.
 */
class HtmlBuilder {
    private $_top; // the trunk of the tree.
    private $_ptr; // pointer within the tree.
    private $_dft; // Distance from the top.

    public function __construct($name = 'html', $attr = array(), $value = '') {
        $this->_top =& new HtmlNode('html', $attr, $value);
        $this->_ptr =& $this->_top;
        $this->_dft = 0;
    }

    public function p($val, $attr = array()) {
        $this->tag('p', $attr, $val);
    }

    public function tag($name, $attr = array(), $value = '', $step_in = false) {
        $tn =& new HtmlNode($name, $attr, $value);
        $this->_ptr->add_node($tn);
        if($step_in) {
            $this->_dft++;
            $this->_ptr =& $tn;
        }
    }

    public function step_out() {
        if(empty($this->_ptr->_parent) or $this->_dft === 0) {
            throw new Exception("No parent to step out to.");
        }
        $this->_ptr =& $this->_ptr->_parent;
        $this->_dft--;
    }

    public function get_string($node = null) {
        if(!empty($node)) {
            $top =& $node;
        } else {
            $top =& $this->_top;
        }
        if(!empty($top->_children)) {
            $out = "\n";
            foreach($top->_children as $c) {
                $out .= $this->get_string($c) . "\n";
            }
            $node->_value .= $out;
        }
        return Html::tag($node->_name, $node->_attributes, $node->_value);
    }
}

class HtmlNode {
    public $_name;
    public $_attributes;
    public $_value;
    public $_children;
    public $_parent;

    public function __construct($name, $attributes=array(), $value='') {
        $this->_name = $name;
        $this->_attributes = $attributes;
        $this->_value = $value;
        $this->_children = array();
        $this->_parent = null;
    }

    public function set_parent(&$node) {
        $this->_parent =& $node;
    }

    public function add_node(&$node) {
        $node->set_parent($this);
        $this->_children[] =& $node;
    }
}
