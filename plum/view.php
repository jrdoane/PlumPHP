<?php
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
