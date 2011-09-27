<?php
namespace Plum;

// Little helper class.
class Init {
    public static function core($module) {
        $dir = dirname(__FILE__);
        $lower = strtolower($module);
        $lower = str_replace("\\", "/", $lower); // Handle deeper namespaces as directories.
        // Example: \Plum\DB\Connection is going to be in /wwwroot/plum/db/db.php
        // Init will be: Init::core('DB\Connection');
        require_once("{$dir}/{$lower}.php");
        if(class_exists("\\Plum\\{$module}")) {
            if(method_exists("\\Plum\\$module", 'init')) {
                $fqn = "\\Plum\\$module";
                $fqn::init();
            }
        }
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
Init::core('Exception');
Init::core('stdClass');
Init::core('Debug');
Init::core('Config');
Init::core('DB');
Init::core('DB\PostgreSQL');
Init::core('URI');
Init::core('HTTP');
Init::core('Html');
Init::core('Controller');
Init::core('View');

Init::app();
