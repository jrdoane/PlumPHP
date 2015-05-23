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

// Little helper class.
class Init {
    static protected $classes_loaded;

    static protected $_app_run_time;
    static protected $_load_times;

    /**
     * Loads a core module.
     *
     * @param string    $module is the name of the component being loaded.
     * @return null
     */
    public static function core($module) {
        if(empty(self::$classes_loaded)) {
            self::$classes_loaded = array();
        }
        $dir = dirname(__FILE__);
        self::load($module, $dir);
    }

    /**
     * Loads an installed PlumPHP extension.
     *
     * @param string    $ext is the name of the extension.
     * @return null
     */
    public static function extension($ext) {
        if(empty(self::$classes_loaded)) {
            self::$classes_loaded = array();
        }
        $dir = dirname(dirname(__FILE__)) . '/ext';
        self::load($ext, $dir);
    }

    public static function load($name, $directory) {
        $time_initial = microtime(true);
        $lower = strtolower($name);
        $lower = str_replace("\\", "/", $lower); // Handle deeper namespaces as directories.
        // Example: \Plum\DB\Connection would translate to /plumroot/plum/db/db.php
        // Init would be: Init::core('DB\Connection');
        $path = "{$directory}/{$lower}.php";
        require_once($path);
        if(class_exists("\\Plum\\{$name}")) {
            if(method_exists("\\Plum\\$name", 'init')) {
                $fqn = "\\Plum\\$name";
                $fqn::init();
            }
        }
        array_push(self::$classes_loaded, $name);
        $time_final = microtime(true);
        self::$_load_times[$name] = $time_final - $time_initial;
    }

    public static function app() {
        $ti = microtime(true);
        $app = Config::get('application_dir', 'system');
        $dirroot = dirname(dirname(__FILE__));
        $approot = "{$dirroot}/{$app}";
        $appinit = "{$approot}/{$app}/init.php";
        if(file_exists($appinit)) {
            include("{$approot}/init.php");
        }
        self::$_load_times['app'] = microtime(true) - $ti;
    }

    /**
     * Goes in reverse through the loaded classes and runs their shutdown method 
     * if it exists. This is very important for classes such as the session 
     * class that needs to write data to the database after everything is done. 
     * This also happens every time so it needs to happen, always...
     */
    public static function system_shutdown() {
        while($mod = array_pop(self::$classes_loaded)) {
            $fqn = "\\Plum\\{$mod}";
            if(!class_exists($fqn)) {
                continue;
            }
            if(method_exists($fqn, 'shutdown')) {
                $fqn::shutdown();
            }
        }
    }

    public static function get_load_time() {
        $t_all = 0;
        foreach(self::$_load_times as $module => $time) {
            $t_all += $time;
        }
        return $t_all;
    }
}

// Starts up Plum. (These are primary class names, file names should be lower case.
// We could wait and see if a page needs any of these modules before loading 
// them, but how much overhead are we really adding? This way we don't need to 
// check to see if anything is loaded (even though we store that anyways.)
// Logger must load first.

/**
 * These are PHP core class wrappers for the Plum namespace and libraries that 
 * need to be loaded first.
 */
Init::core('Logger');
Init::core('Constant'); // Plum defines.
Init::core('Exception');
Init::core('stdClass');
/**
 * Lets begin to load the guts of Plum.
 */
Init::core('Debug');
Init::core('Config');
Init::core('DB');
Init::core('DB\PostgreSQL');
/**
 * Stuff really starts happening here. Before this point we're just getting 
 * ready but once the database connection is initialized the logger will start 
 * using it and processing the backlog from all the data recorded prior to the 
 * connection being initialized.
 *
 * Also before this point the logger is on /SUPER CARP MODE/ which will 
 * eventually (TODO!) output data to either file or standard out. For now we 
 * will be using standard out as this shouldn't ever happen so we're not going 
 * to worry about it.
 */
Init::core('Uri');
Init::core('HTTP');
Init::core('Session');
Init::core('Xml');
Init::core('Html'); // Extends XML, must come aftet.
Init::core('Controller');
Init::core('View');

/**
 * Core components are done. Between now and the time the application code 
 * starts running we will want to initalize any added extensions to plum.
 */
$extensions = Config::get('extensions', 'system');
foreach($extensions as $ext) {
    Init::extension($ext);
}

Init::app();

