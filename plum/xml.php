<?php
namespace Plum;

class Xml {
    public static function tag($name, $attributes, $value) {
        $out = "<$name";
        if(!empty($attributes)) {
            foreach($attributes as $n => $v) {
                $out .= " {$n}=\"{$v}\"";
            }
        }
        if(empty($value)) {
            $out .= " />\n";
            return $out;
        }
        $out .= ">{$value}</$name>";
        return $out;
    }

}

class XmlBuilder {
    private $_top; // the trunk of the tree.
    private $_ptr; // pointer within the tree.
    private $_dft; // Distance from the top.

    public function __construct($name, $attr = array(), $value = '') {
        $this->_top = new HtmlNode('html', $attr, $value);
        $this->_ptr =& $this->_top;
        $this->_dft = 0;
    }

    public function tag($name, $attr = array(), $value = '', $step_in = false, $return = true) {
        if(empty($attr)) {
            $attr = array();
        }
        if(empty($value)) {
            $value = '';
        }
        if(empty($step_in)) {
            $step_in = false;
        }
        $tn = new XmlNode($name, $attr, $value);
        $this->_ptr->add_node($tn);
        if($step_in) {
            $this->_dft++;
            $this->_ptr =& $tn;
        }
        if($return == true) {
            $new_object =& $tn;
        }
        return true;
    }

    /**
     * Step out moves the tree points up a level if $to is null. If $to is set 
     * and is an integer it will attempt to go up that many levels. If $to is 
     * a string it will attempt to go up to the first occurance of that tag.
     *
     * Example: $this->step_out(2) goes up 2 levels.
     * Example: $this->step_out('html') goes up to the top level html tag.
     *
     * @param mixed     $to tells the function how far to step out.
     * @return bool
     */
    public function step_out($to = null) {
        if(empty($this->_ptr->_parent) or $this->_dft === 0) {
            throw new Exception("No parent to step out to.");
        }

        if(!empty($to)) {
            // TODO: write this. :)
            if(is_numeric($to) and is_int($to)) {
            } elseif (is_string($to)) {
            } else {
                throw new ParameterException("Expected int or string and got neither.");
            }
        }
        $this->_ptr =& $this->_ptr->_parent;
        $this->_dft--;
        return true;
    }

    public function get_string($node = null) {
        if(get_class($node) != "Plum\\HtmlNode" AND $node !== null) {
            throw new Exception("Invalid object.");
        }
        if(strtolower(get_class($node)) == 'stdclass') {
            throw new Exception("Invalid object.");
        }
        if($node !== null) {
            $top =& $node;
        } else {
            $top =& $this->_top;
        }
        if(!empty($top->_children)) {
            $out = "\n";
            foreach($top->_children as $c) {
                $out .= $this->get_string($c) . "\n";
            }
            $top->_value .= $out;
        }
        return Html::tag($top->get_name(), $top->get_attributes(), $top->get_value());
    }
}

class XmlNode {
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
        $this->_parent = false;
    }

    public function set_parent(&$node) {
        if(is_subclass_of($node, 'Plum\XmlNode') or $node === false) {
            throw new HtmlNodeExpectedException($node);
        }
        $this->_parent =& $node;
    }

    public function add_node(&$node) {
        if(!self::is_node($node)) {
            throw new HtmlNodeExpectedException($node);
        }
        $node->set_parent($this);
        $this->_children[] =& $node;
    }

    public function get_name() { return $this->_name; }
    public function get_attributes() { return $this->_attributes; }
    public function get_value() { return $this->_value; }

    public static function is_node($node) {
        if(get_class($node) != 'Plum\HtmlNode') {
            return false;
        }
        return true;
    }
}
