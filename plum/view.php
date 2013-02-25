<?php
/**
 * Core PlumPHP Library - View (For displaying output.)
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

class View {

    /**
     * Displays a standard view from the $name and $vars.
     * 
     * @param string    $name is the path to the view with or without .php
     * @param string    $vars are the vars to pass to the view script.
     * @return mixed    A view can specify a return value by setting it in the 
     *                  view class.
     */
    public static function load($name, $vars = array()) {
        // If !ends with php, then add it.
        $name = self::_view_path($name);
        if(!file_exists($name)) {
            throw new Exception("View file missing: {$name}");
        }
        self::_process($name, $vars);
        if($r = self::get_last_return()) {
            return $r;
        }
        return true;
    }

    public static function template($name, $vars = array(), $return=true) {
        $name = self::_view_path($name);
        if(!file_exists($name)) {
            throw new Exception("View file missing: {$name}");
        }
        $text = file_get_contents($name);
        foreach($vars as $varname => $value) {
            $text = preg_replace("/\{{$varname}\}/", $value, $text);
        }

        if($return) {
            return $text;
        }
        print $text;
    }

    private static function _view_path($name) {
        $name = Config::app_root() . "/views/{$name}";
        $name .= preg_match('/.php$|.html$/', $name) ? '' : '.php';
        return $name;
    }

    private static function _process($_file, $_vars = array()) {
        // These globals don't live long.
        global $PAGE, $FILE;
        $PAGE = $_vars;
        $FILE = $_file;

        // This file resides in the global namespace so we can put these 
        // variables in that scope. It's an added step but that is what we get 
        // for using namespaces.
        include(dirname(__FILE__) . '/page.php');
        return true;
    }

    /**
     * View return subsystem.
     */
    private static $last_return_value = null;

    /**
     * Returns the last return value and clears the buffer.
     */
    public static function get_last_return() {
        $i = self::$last_return_value;
        self::$last_return_value = null;
        return $i;
    }

    public static function set_return($anything) {
        self::$last_return_value = $anything;
    }

    /**
     * End view return subsystem.
     */
}
