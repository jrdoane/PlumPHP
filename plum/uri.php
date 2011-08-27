<?php
namespace Plum;

class URI {

    /**
     * These variables are for caching. This is only stored once it is asked 
     * for. If it is never asked for, we never get it.
     */
    private static $_current_controller = '';
    private static $_current_method = '';
    private static $_current_params = '';

    public static function current_uri($full = false) {
        if($full) {
            $root = Config::get('wwwroot');
            $full = "{$root}{$_SERVER['PATH_INFO']}";
        }
        return $_SERVER['PATH_INFO'];
    }

    public static function load() {
        $data = preg_split('/\//', self::current_uri());
        self::$_current_controller = Config::get('default_controller');
        self::$_current_method = Config::get('default_method');
        self::$_current_params = array();
        if(empty($data)) {
            return true;
        }
        if(!is_array($data)) {
            throw new Exception("Expecting array got something else."); // TODO make this less dumb.
        }
        $cl = $cm = false;
        foreach($data as $d) {
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
            self::$_current_controller = Config::get('default_controller');;
        }

        return self::$_current_controller;
    }

    public static function get_function() {
    }

    public static function get_parameters() {
    }
}
