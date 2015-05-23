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
                $v = htmlspecialchars($v);
                $out .= " {$n}=\"{$v}\"";
            }
        }
        if($value === null) {
            $out .= " />";
            return $out;
        } elseif(is_object($value)) {
            throw new Exception("Objects can not be turned into HTML.");
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
    private $_specialchars;
    private $_declaration;

    public function __construct($name, $attr = array(), $value = null) {
        $this->_top = new XmlNode($name, $attr, $value);
        $this->_ptr =& $this->_top;
        $this->_dft = 0;
        $this->_tagcounts = array($name => 1); // For stepped in elements only.
        $this->_specialchars = true;
        $this->_declaration = '<?xml version="1.0" encoding="UTF-8" ?>';
    }

    /**
     * Returns a reference of the currently active XML node.
     *
     * @return object
     */
    public function &get_current_node() {
        return $this->_ptr;
    }

    public function &get_current_children() {
        return $this->_ptr->get_children();
    }

    public function &get_top() {
        return $this->_top;
    }

    public function merge_builders(&$builder, $usetop=false) {
        $btop = $builder->get_top();
        if($usetop) {
            $this->add_child($btop);
        } else {
            $this->add_children($btop->_children);
        }
    }

    public function add_child(&$child) {
        $this->_ptr->add_node($child);
    }

    public function add_children(&$children) {
        foreach($children as &$child) {
            self::add_child($child);
        }
    }

    /**
     * Creates an XML tag.
     * Returns reference of itself ($this), allows method chaining.
     *
     * Value isn't altered in any way shape or form. This method is good if the 
     * XML builder is going to be taking in values with xml already in it.
     *
     * @param string    $name is the name of the tag.
     * @param array     $attr is an array of attributes for the element.
     * @param string    $value is the string to put between the opening and closing tags.
     * @param bool      $step_in determines if the tree pointer should go instead of this new node.
     * @return object
     */
    public function &raw($name, $attr = array(), $value = null, $step_in = false) {
        if(empty($attr)) {
            $attr = array();
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
     * Creates an XML tag.
     * Returns reference of itself ($this), allows method chaining.
     *
     * tag runs htmlspecialchars on the input. If you don't want this use the 
     * `raw` method instead.
     *
     * @param string    $name is the name of the tag.
     * @param array     $attr is an array of attributes for the element.
     * @param string    $value is the string to put between the opening and closing tags.
     * @param bool      $step_in determines if the tree pointer should go instead of this new node.
     * @return object
     */
    public function &tag($name, $attr = array(), $value = null, $step_in = false) {
        if($this->_specialchars and !empty($value)) {
            $value = htmlspecialchars($value);
        }
        return $this->raw($name, $attr, $value, $step_in);
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
     * @param int       $count is used if there is a string. This will determine 
     *                  how many tags to jump over. 1 stays use the first tag, 2
     *                  for a second tag, etc.
     * @return object
     */
    public function &step_out($to = 1, $tagcount = 1) {
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
                while($tagcount != 0) {
                    if(!is_object($this->_ptr->_parent)) {
                        throw new Exception('Object expected, got ' . get_class($this->_ptr->parent));
                    }
                    $this->_ptr =& $this->_ptr->_parent;
                    $this->_dft--;
                    if($this->_ptr->_name == $to) {
                        $tagcount--;
                    }
                }
            } else {
                throw new ParameterException("Expected int or string and got neither.");
            }
        }
        return $this;
    }

    public function &specialchars($enabled) {
        if($enabled) {
            $this->_specialchars = true;
        } else {
            $this->_specialchars = false;
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

        if(!empty($top->_children)) {
            $out = "\n";
            foreach($top->_children as $c) {
                $out .= $this->get_space_depth($depth);
                $out .= $this->get_string($c, $depth + 1);
            }
            $top->_value .= $out . $this->get_space_depth($depth - 1);
        }

        $output = Xml::tag($top->get_name(), $top->get_attributes(), $top->get_value()) . "\n";
        if($depth === 0) {
            $output = $this->_declaration . "\n" . $output;
        }
        return $output;
    }

    /**
     * Returns the proper amount of white-space for the depth of the tree.
     * WARNING: Do not check the result of this with empty().
     *
     * @param int       $depth is how deep in the tree we are.
     * @return string
     */
    private function get_space_depth($depth) {
        $out = '';
        for($i = 0; $i <= $depth; $i++) {
            $out .= '  '; // Add 2 spaces for every node we go in.
        }
        return $out;
    }

    public function set_declaration($str) {
        $this->_declaration = $str;
    }

    // TODO: Make this check parent claseses for these two.
    public static function is_builder($b) {
        if(empty($b)) {
            return false;
        }
        if(!is_object($b)) {
            return false;
        }
        $classes = array(
            'Plum\HtmlBuilder',
            'Plum\XmlBuilder'
        );
        if(in_array(get_class($b), $classes)) {
            return true;
        }
        return false;
    }
}

/**
 * OOP representation of an XML node. This is used by the XMLBuilder.
 */
class XmlNode {
    public $_name;
    public $_attributes;
    public $_value;
    public $_children;
    public $_parent;

    public function __construct($name, $attributes=array(), $value=null) {
        $this->_name = $name;
        $this->_attributes = $attributes;
        $this->_value = $value;
        $this->_children = array();
        $this->_parent = false;
    }

    /**
     * Sets the parent node of this node.
     * WARNING: Settings a parent that is a child will cause recursion and 
     * chaotic behavior.
     * 
     * @param object    &$node is an XML node to set as a parent. (by reference.)
     * @return null
     */
    public function set_parent(&$node) {
        if(is_subclass_of($node, 'Plum\XmlNode') or $node === false) {
            throw new HtmlNodeExpectedException($node);
        }
        $this->_parent =& $node;
    }

    /**
     * Adds a child node to the current node.
     *
     * @param object    $node is an XML node.
     * @return null
     */
    public function add_node(&$node) {
        if(!self::is_node($node)) {
            throw new XmlNodeExpectedException($node);
        }
        $node->set_parent($this);
        $this->_children[] =& $node;
    }

    /**
     * Returns the tag name.
     *
     * @return string
     */
    public function get_name() { return $this->_name; }

    /**
    * Gets an array of attributs for a node. Returns an empty array if no 
    * attributes.
    *
    * @return array
     */
    public function get_attributes() { return $this->_attributes; }

    /**
     * Sets a value to an attributes on this html node.
     *
     * @param string    $attribute_name is exactly how it sounds in relation to 
     *                  an html node.
     * @param mixed     $value is a value to put in this attribute.
     * @return null
     */
    public function set_attribute($attribute_name, $value) {
        if($value === null) {
            unset($this->attribute[$attribute_name]);
        }
        $this->attributes[$attribute_name] = $value;
    }

    /**
     * Gets the contents of the current HTML flag (excluding explicit HTML 
     * children.)
     *
     * @return string
     */
    public function get_value() { return $this->_value; }

    /**
     * Gets the children of the current node.
     *
     * @return array
     */
    public function &get_children() { return $this->_children; }

    /**
     * Is this an XML node?
     * TODO: Overhaul this function to check and see if the class extends 
     * XMLNode.
     *
     * @param object    $node is ab object to see if it is a XMLNode.
     * @return bool
     */
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
