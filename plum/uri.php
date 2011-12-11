<?php
/**
 * Core PlumPHP Class - URI (For site navigation.)
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

class Uri {

    /**
     * These variables are for caching. This is only stored once it is asked 
     * for. If it is never asked for, we never get it.
     */
    private static $_current_controller = '';
    private static $_current_method = '';
    private static $_current_params = '';

    public static function base() {
        return Config::get('wwwroot', 'web');
    }

    public static function current_uri($full = false) {
        $root = Config::get('wwwroot', 'web');
        $file = Config::get('wwwfile', 'web');
        if($full) {
            if(!isset($_SERVER['PATH_INFO'])) {
                return "{$root}{$file}";
            }
            $full = "{$root}{$file}{$_SERVER['PATH_INFO']}";
            return $full;
        }
        if(!isset($_SERVER['PATH_INFO'])) {
            return '';
        }
        return $_SERVER['PATH_INFO'];
    }

    public static function init() {
        $data = preg_split('/\//', self::current_uri());
        self::$_current_controller = Config::get('default_controller', 'web');
        self::$_current_method = Config::get('default_method', 'web');
        self::$_current_params = array();
        if(empty($data)) {
            return true;
        }
        if(!is_array($data)) {
            throw new Exception("Expecting array got something else."); // TODO make this less dumb.
        }
        $cl = $cm = false;
        foreach($data as $d) {
            if(empty($d)) {
                continue;
            }
            if($cl == false) {
                self::$_current_controller = $d;
                $cl = true;
                continue;
            }
            if($cm == false) {
                self::$_current_method = $d;
                $cm = true;
                continue;
            }
            self::$_current_params[] = $d;
        }
    }

    public static function get_controller() {
        if(!empty(self::$_current_controller)) {
            return self::$_current_controller;
        }
        $data = preg_split('/\//', self::current_uri());
        if(empty($data)) {
            self::$_current_controller = Config::get('default_controller', 'web');
        }

        return self::$_current_controller;
    }

    public static function get_request_array() {
        $ra = array();
        $ra['controller'] = self::get_controller();
        $ra['method'] = self::get_method();
        $ra['parameters'] = self::get_parameters();

        return $ra;
    }

    public static function get_method() {
        return self::$_current_method;
    }

    public static function get_parameters() {
        return self::$_current_params;
    }
}
