<?php
namespace Plum\DB;

/**
 * Defines the connection.
 */
abstract class Connection {
    protected $_connection;
    protected $_prefix;

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

    /**
     * Replaces tables in curly braces with a prepended table prefix.
     * Example: {table_name} will become prefix_table_name assuming prefix is 
     * "prefix_"
     */
    public function apply_table_prefix($sql) {
        $prefix = $this->get_prefix();
        $i = $this->table_identifier();
        return preg_replace('/\{([a-z][a-z0-9_]*)\}/', "{$i}{$prefix}".'$1'."{$i}", $sql);
    }

    public function apply_sql_parameters($sql, $params) {
        $qpc = substr_count($sql, '?');
        if(count($params) != $qpc) {
            throw new \Plum\Exception('Invalid number of parameters.');
        }

        for($i = 1; $i <= count($params); $i++) {
            $sql = preg_replace('/\?/', '\$'.$i, $sql, 1);
        }

        return $sql;
    }

    public function build_value_params($params) {
        $vpa = array();
        for($i = 0; $i < count($params); $i++) {
            $vpa[] = '?';
        }
        return "\nVALUES\n" . '(' . implode(' , ', $vpa) . ")\n";
    }

    /**
     * Preps a table name, puts identifiers around the table name and prepends 
     * the table prefix.
     *
     * @param string    $table is a table name.
     * @return string
     */
    public function prep_table_name($table) {
        $i = $this->table_identifier();
        return $i.$this->_prefix.$table.$i;
    }

    abstract public function connect($user, $password, $database, $server, $port, $persistant);
    abstract public function get_prefix();
    abstract public function table_identifier();
    abstract public function process_input($var);
    
    /**
     * SQL caller method.
     */
    abstract public function sql($sql, $params = array(), $rs=false);
    abstract public function select_sql($sql, $params = array(), $limit=0, $offset=0, $rs=false);
    abstract public function select_count($table, $where);
    abstract public function select_count_sql($sql, $params = array());

    /**
     * SQL wrappers for basic sql commands.
     */
    public abstract function insert($table, $data, $return=false, $rs=false);
    public abstract function delete($table, $where, $return=false, $rs=false);
    public abstract function select($table, $where=array(), $limit=0, $offset=0, $sort='', $rs=false);
    public abstract function update($table, $data, $where, $return=false, $rs=false);
}

/**
 * Defines the results of executed SQL queries.
 * (This should be the case for any sql that returns data.)
 */
abstract class Result {
    protected $_result;

    /**
     * Not everyone wants to get a result object back. In fact more times than 
     * not it isn't what the programmer wants. So this method will take in 
     * a result and morph it into something more usable.
     *
     * Queries that don't return rows will return a bool.
     * Queries that return rows will return an array, even if no records are 
     * returned.
     * Otherwise a false will be returned.
     *
     * @param bool      $obj determines if records are returned as objects or arrays.
     * @param int       $return specifies how many records to return if some are 
     *                  encountered.
     * @return mixed
     */
    public abstract function simplify($obj=true, $return=null);

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

