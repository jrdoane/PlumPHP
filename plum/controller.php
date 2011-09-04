<?php
namespace plum;

/**
 * What a controller does. You always need to inherit this.
 */

abstract class Controller {
    /**
     * Controller variables.
     */
    protected $_page;

    /**
     * Methods required by all controllers but start here.
     */
    public function __construct() {
        $this->_page = new \stdClass;
    }

    /**
     * Functions used by the controller but not required.
     */
    public function before() { return true; }
    public function after() { return true; }

    /**
     * Static functions.
     */

    /**
     * Think of this is a factory function to load and initialize the current 
     * controller. This requires fetching config data to get the full name and 
     * path.
     */
    public static function factory($name) {
        $cprefix = Config::get('controller_prefix', 'system');
        $csuffix = Config::get('controller_suffix', 'system');
        $appdirname = Config::get('application_dir', 'system');
        $controllerdirname = Config::get('controller_dir', 'system');
        $dir = dirname(dirname(__FILE__)) . "/{$appdirname}/{$controllerdirname}";
        $classname = "{$cprefix}{$name}{$csuffix}";
        $dir .= "/{$classname}.php";
        if(!file_exists($dir)) {
            throw new Exception("Missing controller: {$classname}.");
        }
        include_once($dir);
        if(!class_exists($classname)) {
            throw new Exception("Missing controller: {$classname}.");
        }
        return new $classname();
    }
}
?>
