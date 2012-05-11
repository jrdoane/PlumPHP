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
    protected $_cached_validation;

    public function __construct($action, $method='POST', $header='') {
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
        if(!empty($header)) {
            $this->_html->fieldset();
            $this->_html->legend($header);
        }
        $this->_fields = array();
        $this->_validation = array();
        $this->_cached_validation = null;

        // Form ready to be assembled.
        $this->assemble();
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
        $output = array();
        foreach($this->_fields as $field) {
            if(!empty($field->_attributes) & !empty($field->_attributes['name'])) {
                $name = $field->_attributes['name'];
                $input = HTTP::input($name);
                if(isset($input)) {
                    $output[$name] = $input;
                }
            }
        }

        if(count($output) == 0) {
            return false;
        }

        return (object)$output;
    }

    public function get_valid_data() {
        if(!$this->validate()) {
            return false;
        }
        return $this->get_data();
    }

    /**
     * Using defined rules, check to see if the input validates.
     */
    public function validate() {
        $data = $this->get_data();
        foreach($this->_validation as $name => $rule) {
            switch($rule->type) {
            case \Plum\FORM_RULE_REQUIRED:
                if(empty($data->$name)) {
                    return false;
                }
                break;
            case \Plum\FORM_RULE_NUMERIC:
                if(empty($data->$name)) {
                    return false;
                }
                if(!is_numeric($data->$name)) {
                    return false;
                }
                break;
            case \Plum\FORM_REGEX:
                if(!preg_match($rule->rule, $data->$name)) {
                    return false;
                }
                break;
            default:
                throw new Exception('unknown rule ('.var_export($rule->type, true).')');
            }
        }
        return true;
    }

    public function add($html_name, $attr, $label_text='', $nowrap=false) {
        if(empty($attr) & is_array($attr)) {
            new Exception();
        }
        $short_tag = null;
        $tag = 'input';
        if($html_name == 'textarea') { $tag = $html_name; $short_tag = ''; }
        else { $attr['type'] = $html_name; }
        $node = new HtmlNode($tag, $attr, $short_tag);
        // Only check fields with names provided. Otherwise we won't watch the 
        // field for data or validation.
        if(!empty($attr['name'])) {
            $name = $attr['name'];
            $this->_fields[$name] =& $node;
        } else {
            $attr['name'] = '';
        }

        $this->_html->div(array('class' => 'form_row'));
        if(strlen($label_text) != 0) {
            $this->_html->label($label_text, $attr['name']);
        }

        $this->_html->add_child($node);

        $this->_html->step_out('fieldset');

        if(!$nowrap and $html_name != 'hidden') {
            $this->_html->br();
        }
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
            $this->_validation->$name = array();
        }
        $v =& $this->_validation->$name;
        $v[] = $rule_obj;
    }

    public function get_builder() {
        return $this->_html;
    }

    public function get_html() {
        return $this->_html->get_string();
    }

    public static function all_rules() {
        return array(
            \Plum\FORM_REQUIRED,
            \Plum\FORM_NUMERIC,
            \Plum\FORM_REGEX
        );
    }
}

