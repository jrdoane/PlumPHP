<?php
/**
 * Core PlumPHP class - DB (Database manager)
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
require_once(dirname(__FILE__) . "/db/lib.php");

/**
 * Database interface.
 */
class DB {
    protected static $connections;
    protected static $default;

    /**
     * It's an init thing. See init.php.
     */
    static function init() {
        $plum_dir = dirname(__FILE__);
        self::$connections = array();
        if(!($servers = Config::get('servers', 'db'))) {
            return true;
        }

        /**
         * Initialize all DB connections.
         */
        foreach($servers as $config_name => $server) {
            $type = strtolower($server['dbtype']);
            require_once("{$plum_dir}/db/{$type}.php");
            $fqn = "\\Plum\\DB\\{$server['dbtype']}\\Connection";
            $connection = new $fqn($server);
            $server['connection'] = $connection;
            if($server['default']){
                self::$default = $server;
            }
            self::$connections[$config_name] = $server;
        }
    }

    /**
     * Gets the current connection. Default is returned if no name is provided. 
     * Returns false is none exists with that name.
     */
    static function get_conn($name='') {
        if(!empty($name)) {
            return isset(self::$connections[$name]) ? self::$connections[$name]['connection'] : false;
        }

        return self::$default['connection'];
    }

    static function exec_conn($sql, $name='') {
        $conn = self::get_conn($name);
        return $conn->sql($sql);
    }
}
