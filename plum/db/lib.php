<?php
namespace Plum\DB;

/**
 * Defines the connection.
 */
abstract class Connection {
    protected $_connection;

    function __construct($server) {
        $this->connect(
            $server['username'],
            $server['password'],
            $server['database'],
            $server['server'],
            $server['port'],
            $server['persistant'],
            $server['prefix']
        );
    }

    abstract public function connect($user, $password, $database, $server, $port, $persistant);
    abstract public function table_identifier();
    
    /**
     * SQL caller method.
     */
    abstract public function sql($sql);

    /**
     * SQL wrappers for basic sql commands.
     */
    public abstract function insert($table, $data, $return=false);
    public abstract function delete($table, $where, $return=false);
    public abstract function select($table, $where=array(), $limit=0, $offset=0, $sort='');
    public abstract function update($table, $data, $where, $return=false);
}

/**
 * Defines the results of executed SQL queries.
 * (This should be the case for any sql that returns data.)
 */
abstract class Result {
    protected $_result;

    public abstract function __construct($query);
    public abstract function status();
    public abstract function success();
    public abstract function count_rows_altered();
    public abstract function count_rows_inserted();
    public abstract function count_rows_returned();
    public abstract function get_rows_inserted();
    public abstract function has_next();
    public abstract function get_next();
    public abstract function get_all_assoc();
    public abstract function get_all_obj();
}
