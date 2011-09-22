<?php
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
            if($server['default'] and empty(self::$default)){
                self::$default = $server;
            } else {
                self::$connections[$config_name] = $server;
            }
        }
    }

    /**
     * Gets the current connection. Default is returned if no name is provided. 
     * Returns false is none exists with that name.
     */
    static function get_conn($name='') {
        if(!empty($name)) {
            return isset(self::$connections[$name]) ? self::$connections[$name] : false;
        }

        foreach(self::$connections as $key => &$c) {
            if($c
        }
    }

    static function exec_conn($name = '', $sql) {
    }
}
