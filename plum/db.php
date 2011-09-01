<?php
namespace Plum;

/**
 * Defines the connection.
 */
abstract class DB_Connection {
    protected $_connection;

    function __construct() {
        $this->init(
            Config::get('server', 'db'),
            Config::get('port', 'db'),
            Config::get('user', 'db'),
            Config::get('password', 'db'),
            Config::get('database', 'db')
        );
    }

    abstract function connect();
    abstract function init($server, $port, $username, $password, $db);
    abstract function sql($sql);
    abstract function get_table_identifier();
}

/**
 * Defines the results of executed SQL queries.
 * (This should be the case for any sql that returns data.)
 */
abstract class DB_Result {
    protected $_result;

    abstract function status();
    abstract function count_rows_altered();
    abstract function count_rows_inserted();
    abstract function get_rows_inserted();
    abstract function has_next();
    abstract function get_next();
    abstract function get_all_assoc();
    abstract function get_all_obj();
}

/**
 * Database interface.
 */
class DB {
    protected static $connections;

    /**
     * It's an init thing. See init.php.
     */
    static function init() {
        self::$connections = array();
    }
}
