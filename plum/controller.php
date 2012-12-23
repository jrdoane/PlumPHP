<?php
/**
 * Core PlumPHP Libary - Controller
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
 * What a controller does. You always need to inherit this.
 */

class Controller {
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
        if(empty($name)) {
            throw new MissingParameterException($name);
        }
        $name = strtolower($name);
        $cprefix = Config::get('controller_class_prefix', 'system');
        $csuffix = Config::get('controller_class_suffix', 'system');
        $appdirname = Config::get('application_dir', 'system');
        $controllerdirname = Config::get('controller_dir', 'system');
        $dir = dirname(dirname(__FILE__)) . "/{$appdirname}/{$controllerdirname}";
        $classname = "{$cprefix}{$name}{$csuffix}";
        $dir .= "/{$classname}.php";
        if(!file_exists($dir)) {
            throw new Exception("Missing controller file: {$dir}.");
        }
        include_once($dir);
        if(!class_exists($classname)) {
            throw new Exception("Missing controller class: {$classname}.");
        }
        return new $classname();
    }
}
?>
