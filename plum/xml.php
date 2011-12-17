<?php
/**
 * Core PlumPHP Library - XML
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

class Xml {
    public static function tag($name, $attributes, $value) {
        $out = "<$name";
        if(!empty($attributes)) {
            foreach($attributes as $n => $v) {
                $out .= " {$n}=\"{$v}\"";
            }
        }
        if(empty($value)) {
            $out .= " />";
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
    private $_tagcounts;

    public function __construct($name, $attr = array(), $value = '') {
        $this->_top = new XmlNode($name, $attr, $value);
        $this->_ptr =& $this->_top;
        $this->_dft = 0;
        $this->_tagcounts = array($name => 1); // For stepped in elements only.
    }

    /**
     * Creates an XML tag.
     * Returns reference of itself, allows method chaining.
     *
     * WARNING! Unpredictable behavior when using return while chaining.
     */
    public function &tag($name, $attr = array(), $value = '', $step_in = false) {
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
        if(!is_object($this->_ptr)) {
            throw new Exception(var_export($this, true));
        }
        $this->_ptr->add_node($tn);
        if($step_in) {
            $this->_dft++;
            $this->_ptr =& $tn;

            if(empty($this->_tagcounts[$name])) {
                $this->_tagcounts[$name] = 1;
            } else {
                $this->_tagcounts[$name]++;
            }
        }
        return $this;
    }

    /**
     * Gets the distance from the top of the tree.
     * Important for indenting xml output.
     * 
     * @return int
     */
    public function get_dft() {
        return $this->_dft;
    }

    /**
     * Step out moves the tree points up a level if $to is null. If $to is set 
     * and is an integer it will attempt to go up that many levels. If $to is 
     * a string it will attempt to go up to the first occurance of that tag.
     *
     * This returns a reference to the builder object, like tag.
     *
     * Example: $this->step_out(2) goes up 2 levels.
     * Example: $this->step_out('html') goes up to the top level html tag.
     *
     * @param mixed     $to tells the function how far to step out.
     * @return object
     */
    public function step_out($to = 1) {
        if(empty($this->_ptr->_parent) or $this->_dft === 0) {
            throw new Exception("No parent to step out to.");
        }

        if(!empty($to)) {
            // TODO: write this. :)
            if(is_numeric($to) and is_int($to)) {
                if($to > $this->_dft) {
                    throw new Exception("Attempt to step out by {$to}, only have {$this->_dft}.");
                }
                for($n = 1; $n <= $to; $n++) {
                    if(empty($this->_ptr->_parent)) {
                        throw new Exception('Objected expected, got ' . get_class($this->_ptr->parent));
                    }
                    $this->_ptr =& $this->_ptr->_parent;
                    $this->_dft--;
                }
            } elseif (is_string($to)) {
                if(empty($this->_tagcounts[$to])) {
                    throw new Exception("Unable to go back to '{$to}', never stepped into that tag.");
                }

                // We're going to go back until we hit the tag we want.
                do {
                    if($this->_ptr->_name == $to) {
                        break;
                    }
                    if(!is_object($this->_ptr->_parent)) {
                        throw new Exception('Object expected, got ' . get_class($this->_ptr->parent));
                    }
                    $this->_ptr =& $this->_ptr->_parent;
                    $this->_dft--;
                } while ($this->_ptr->_name != $to);
            } else {
                throw new ParameterException("Expected int or string and got neither.");
            }
        }
        return $this;
    }

    public function get_string($node = null, $depth = 0) {
        if(!XmlNode::is_node($node) and $node !== null) {
            throw new Exception("Invalid object.");
        }

        // What does this do? --jdoane
        if($node !== null) {
            $top =& $node;
        } else {
            $top =& $this->_top;
        }
        // We want to make sure everything is XML/HTML friendly before we start 
        // adding XML with it which would make this impossible any later.
        $top->_value = htmlspecialchars($top->_value);

        if(!empty($top->_children)) {
            $out = "\n";
            foreach($top->_children as $c) {
                $out .= $this->get_space_depth($depth);
                $out .= $this->get_string($c, $depth + 1);
            }
            $top->_value .= $out . $this->get_space_depth($depth - 1);
        }
        return Xml::tag($top->get_name(), $top->get_attributes(), $top->get_value()) . "\n";
    }

    private function get_space_depth($depth) {
        $out = '';
        for($i = 0; $i <= $depth; $i++) {
            $out .= '  '; // Add 2 spaces for every node we go in.
        }
        return $out;
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
            throw new XmlNodeExpectedException($node);
        }
        $node->set_parent($this);
        $this->_children[] =& $node;
    }

    public function get_name() { return $this->_name; }
    public function get_attributes() { return $this->_attributes; }
    public function get_value() { return $this->_value; }

    public static function is_node($node) {
        if(empty($node)) {
            return false;
        }
        $classes = array(
            'Plum\HtmlNode',
            'Plum\XmlNode'
        );
        if(in_array(get_class($node), $classes)) {
            return true;
        }
        return false;
    }
}
