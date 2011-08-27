<?php
namespace Plum;

class Config {
    private static $_cfg; // Contains all config data parsed in at init.
    private static $_default_module = 'web';
    /**
     * Gets a configuration item. Module refers to the name of the config file.
     * TODO: Take in an array for an id to traverse an array in the config.
     */
    public static function get($id, $module = self::$_default_module) {
        if(!isset(self::$_cfg[$module])) {
            throw new Exception("Missing config module."); // TODO: Make this less dumb.
        }
        if(!isset(self::$_cfg[$module][$id])) {
            return false;
        }
        return self:$_cfg[$module][$id];
    }

    /**
     * Automatically scans the config folder and loads in all the files.
     * php extensions are stripped then remainder is used for module name.
     *
     * This is unique to PHP5.3 as we're loading config data into the class
     * as a static variable. An instance of this class will never be made. :)
     *
     * @return int      number of files loaded into config class.
     */
    public static function load() {
        self::$_cfg = array();
        $dir = dirname(dirname(__FILE__)) . '/config';
        $files = scandir($dir);
        $count = 0;
        foreach($files as $f) {
            if($f = '.' or $f = '..') {
                continue;
            }
            $config = array();
            include("{$dir}/{$f}");
            $_cfg[$f] = $config;
            $count++;
        }
        return $count;
    }
}
