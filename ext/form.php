<?php
/**
 * Form Library - PlumPHP Extension
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

define('Plum\FORM_RULE_REQUIRED', 1);
define('Plum\FORM_RULE_NUMERIC', 2);
define('Plum\FORM_RULE_REGEX', 3);

/**
 * Form class, will contain a builder.
 */
abstract class Form {
    protected $_html;
    protected $_fields;
    protected $_validation;

    public function __construct($action, $method='POST') {
        if(is_array($action)) {
            if(empty($action['method'])) {
                $action['method'] = $method;
            }
        } else {
            $action = array(
                'action' => $action,
                'method' => $method
            );
        }

        $this->_html = new HtmlBuilder('form', $action);
        $this->_fields = array();
        $this->_validation = array();
    }

    /**
     * This method must be overriden.
     * assemble is responsible for describing what the form is and what is in 
     * it.
     */
    public abstract function assemble();

    /**
     * Using defined fields, get the data submitted from the form, if it exists.
     */
    public function get_data() {
    }

    /**
     * Using defined rules, check to see if the input validates.
     */
    public function validate() {
    }

    public function add($html_name, $attr, $label_text='', $rule=null) {
        if(empty($attr) & is_array($attr)) {
            new Exception();
        }
        $node = new HtmlNode($input, $name, $attr);
        $this->_fields[$name] =& $node;
        $this->_html->add_child($node);
    }

    public function add_rule($name, $type, $rule) {
        $rule_obj = (object)array(
            'type' => $type,
            'rule' => $rule
        );
        if(empty($this->_validation)) {
            $this->_validation = new stdClass;
        }
        if(empty($this->_validation[$name])) {
            $this->_validation->$name = array()
        }
        $this->_validation->$name[] = $rule_obj;
    }

    public static function all_rules() {
        return array(
            \Plum\FORM_REQUIRED,
            \Plum\FORM_NUMERIC,
            \Plum\FORM_REGEX
        );
    }
}

