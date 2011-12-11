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
     * @return bool     Success?
     */
    public static function load($name, $vars = array()) {
        // If !ends with php, then add it.
        $name = Config::app_root() . "/views/{$name}";
        $name .= preg_match('/.php$/', $name) ? '' : '.php';
        if(!file_exists($name)) {
            throw new Exception("View file missing: {$name}");
        }
        self::_process($name, $vars);
    }

    private static function _process($_file, $_vars = array()) {
        foreach($_vars as $_key => $_v) {
            $$_key = $_v;
        }
        include($_file);
        return true;
    }
}
