<?php
namespace Plum;

// Little helper class.
class Init {
    static protected $modules_loaded;

    public static function core($module) {
        if(empty(self::$classes_loaded)) {
            self::$modules_loaded = array();
        }
        $dir = dirname(__FILE__);
        $lower = strtolower($module);
        $lower = str_replace("\\", "/", $lower); // Handle deeper namespaces as directories.
        // Example: \Plum\DB\Connection is going to be in /wwwroot/plum/db/db.php
        // Init will be: Init::core('DB\Connection');
        $path = "{$dir}/{$lower}.php";
        require_once($path);
        if(class_exists("\\Plum\\{$module}")) {
            if(method_exists("\\Plum\\$module", 'init')) {
                $fqn = "\\Plum\\$module";
                $fqn::init();
            }
        }
        self::$modules_loaded[$module] = $path;
    }

    public static function app() {
        $app = Config::get('application_dir', 'system');
        $dirroot = dirname(dirname(__FILE__));
        $approot = "{$dirroot}/{$app}";
        $appinit = "{$approot}/{$app}/init.php";
        if(file_exists($appinit)) {
            include("{$approot}/init.php");
        }
    }
}

// Starts up Plum. (These are primary class names, file names should be lower case.
// We could wait and see if a page needs any of these modules before loading 
// them, but how much overhead are we really adding? This way we don't need to 
// check to see if anything is loaded (even though we store that anyways.)
// Logger must load first.

/**
 * These are PHP core class wrappers for the Plum namespace.
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
Init::core('Xml');
Init::core('Html'); // Extends XML, must come aftet.
Init::core('Controller');
Init::core('View');

Init::app();
