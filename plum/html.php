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
        $this->set_declaration(\Plum\HTML::doctype());
    }

    public function &head() {
        return $this->tag('head', array(), '', true);
    }

    public function &script_src($src) {
        return $this->tag('script', array('src' => $src), ' ', false);
    }

    public function &script_str($js) {
        return $this->raw('script', array(), $js, false);
    }

    public function &meta($attr = array()) {
        return $this->tag('meta', $attr, null, false);
    }

    /**
     * Simple title wrapper for the title tag. It takes a string or no string, 
     * and will do some string magic if you would like the site name in the 
     * title which defaults to enabled.
     *
     * @param string    $string is some text that will become the title.
     * @param bool      $prepend if true (default) will add the site name.
     */
    public function &title($string = '', $prepend = true) {
        if($prepend) {
            $site = Config::get('site_name_short', 'web');
            if(empty($string)) {
                $string = $site;
            } else {
                $string = $site . ': ' . $string;
            }
        }
        return $this->tag('title', array(), $string);
    }

    public function &body() {
        return $this->tag('body', array(), '', true);
    }

    /**
     * Anchor (chain)method. Builds an anchor for you.
     *
     * @param string    $text is what is inside the a tag.
     * @url mixed       $url is either a string, which will be the href 
     *                  attribute, or will take an array of attributes.
     * @return object
     */
    public function &a($text, $attr=array()) {
        if(is_string($attr)) {
            $attr = array('href' => $attr);
        }

        return $this->tag('a', $attr, $text);
    }

    public function &h($level, $text, $attr = array()) {
        if(!is_numeric($level)) {
            throw new Exception();
        }
        return $this->tag("h{$level}", $attr, $text);
    }

    public function &p($val='', $attr = array(), $step_in=false) {
        return $this->tag('p', $attr, $val, $step_in);
    }

    public function &img($attr) {
        if (is_string($attr)) {
            $attr = array('src' => $attr);
        }
        return $this->tag('img', $attr);
    }

    public function &div($attr = array()) {
        return $this->tag('div', $attr, '', true);
    }

    public function &br() {
        return $this->tag('br');
    }

    public function &hr($id='') {
        return $this->tag('hr', array('id' => $id));
    }

    public function &span($text, $attr = array()) {
        return $this->tag('span', $attr, $text);
    }

    public function &pre($val, $attr = array()) {
        return $this->tag('pre', $attr, $val);
    }

    public function &form($attr) {
        return $this->tag('form', $attr, '', true);
    }

    public function &fieldset($attr = array()) {
        return $this->tag('fieldset', $attr, '', true);
    }

    public function &legend($text, $attr = array()) {
        return $this->tag('legend', $attr, $text);
    }

    public function &label($text, $for = '', $attr = array()) {
        if(!empty($for)) {
            $attr['for'] = $for;
        }
        return $this->tag('label', $attr, $text);
    }

    /**
     * New and improved input function.
     *
     * @param string    $type is an html form type string.
     * @param mixed     $attr can be a name or an array of attributes.
     * @return object
     */
    public function &input($type, $attr = array()) {
        if(is_string($attr)) {
            $attr = array('name' => $attr);
        }
        if(!is_array($attr)) {
            throw new InvalidParameterTypeException($attr);
        }
        $attr['type'] = $type;
        return $this->tag('input', $attr);
    }

    public function &table($attr = array()) {
        return $this->tag('table', $attr, '', true);
    }

    public function &tr($attr = array()) {
        return $this->tag('tr', $attr, '', true);
    }

    public function &th($text, $attr = array()) {
        return $this->tag('th', $attr, $text);
    }

    public function &td($text='', $attr = array()) {
        $step_in = false;
        if(strlen($text) == 0) {
            $step_in = true;
        }
        return $this->tag('td', $attr, $text, $step_in);
    }

    public function &ol($attr = array()) {
        return $this->tag('ol', $attr, '', true);
    }

    public function &ul($attr = array()) {
        return $this->tag('ul', $attr, '', true);
    }

    /**
     * li() is a little different than the average html method. If text is exactly false 
     * (which is the default,) the HTMLBuilder pointer will be moved into the 
     * tag. If text is a string, it will make the tag without stepping into it.
     *
     * @param mixed     $text has been explained above.
     * @return object
     */
    public function &li($text=false, $attr = array()) {
        if($text === false) {
            return $this->tag('li', $attr, '', true);
        }
        return $this->tag('li', $attr, $text);
    }

    public function &link($attr) {
        return $this->tag('link', $attr);
    }

    public function &link_style($url) {
        $attr = array(
            'href' => $url,
            'rel' => 'stylesheet',
            'type' => 'text/css',
            'media' => 'screen'
        );
        return $this->link($attr);
    }

}

// Add any Html tag specific things here.
class HtmlNode extends XmlNode { }

