<?php
/**
 * Core PlumPHP Libary - Initialization
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
 * Language class, will enable string translations.
 */

class Lang {
    protected static $_strings;

    public static function init() {
        $plumdir = Config::get('plum_dir', 'lang');
        $lang = Config::get('lang', 'lang');
        $plumdir .= "/{$lang}";
        self::load($plumdir);
        foreach(Config::get('app_dirs', 'lang') as $ld) {
            $ld .= "/{$lang}";
            self::load($ld);
        }
    }

    public static function load($path) {
	if(!is_dir($path)) {
            return;
	}
        $files = scandir($path);
        foreach($files as $f) {
            if($f == '.' or $f == '..' or !preg_match('/.php$/', $f)) {
                continue;
            }
            
            preg_match("/(^.*).php$/", $f, $match);
            if(empty(self::$_strings[$match[1]])) {
                self::$_strings[$match[1]] = array();
            }
            $string = array();
            include($path . "/{$f}");
            self::$_strings[$match[1]] = array_merge(self::$_strings[$match[1]], $string);;
        }
    }

    public static function get($string, $module='') {
        if(empty($module)) {
            $module = Config::get('default_file', 'lang');
        }
        // Did we already load the file?
        if(empty(self::$_strings[$module])) {
            // TODO: Write to logger that a missing language module was 
            // accessed.
            return "[[$string]]";
        }

        if(empty(self::$_strings[$module][$string])) {
            return "[[$string]]";
        }

        return self::$_strings[$module][$string];
    }
}
