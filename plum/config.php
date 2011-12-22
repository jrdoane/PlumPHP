<?php
/**
 * Core PlumPHP Library File - Configuration Class
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

class Config {
    private static $_cfg; // Contains all config data parsed in at init.
    private static $_default_module = 'web';
    /**
     * Gets a configuration item. Module refers to the name of the config file.
     * TODO: Take in an array for an id to traverse an array in the config.
     */
    public static function get($id, $module = null) {
        if($module == null) {
            $module = self::$_default_module;
        }
        if(!isset(self::$_cfg->$module)) {
            throw new Exception("Missing config module."); // TODO: Make this less dumb.
        }
        $mdata = self::$_cfg->$module;
        if(!isset($mdata[$id])) {
            return false;
        }
        return $mdata[$id];
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
    public static function init() {
        self::$_cfg = new stdClass;
        $dir = dirname(dirname(__FILE__)) . '/config';
        $files = scandir($dir);
        $count = 0;
        foreach($files as $f) {
            if($f == '.' or $f == '..') {
                continue;
            }
            $config = array();
            include("{$dir}/{$f}");
            $f = preg_replace('/.php/', '', $f);
            self::$_cfg->$f = $config;
            $count++;
        }
        return $count;
    }

    public static function app_root() {
        return dirname(dirname(__FILE__)) . '/' . self::get('application_dir', 'system');
    }

    public static function www_root() {
        return dirname(dirname(__FILE__));
    }

    public static function plum_root() {
        return dirname(dirname(__FILE__)) . '/plum';
    }

    public static function ext_root() {
        return dirname(dirname(__FILE__)) . '/ext';
    }
}
