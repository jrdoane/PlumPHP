<?php
namespace Plum\DB\PostgreSQL;
use \Plum\DB\Connection as ConnectionShell;
use \Plum\DB\Result as ResultShell;
use \Plum\DB\Query as QueryShell;

class Connection extends ConnectionShell {
    public function connect($user, $password, $database, $server = 'localhost', $port = 5432, $persistant = false) {
        $connection_string = "dbname={$database} port={$port} host={$server} ";
        $connection_string .= "user={$user} password={$password}";
        $this->_connection = pg_connect($connection_string);
    }

    public function sql($sql) {
        $result = pg_query($this->_connection, $sql);
        if($result == false) {
            $error = pg_last_error();
            throw new \Plum\Exception("PostgreSQL Query Error: {$error}");
        }
        return new Result($result);
    }

    public function table_identifier() {
        return '"';
    }

    public function insert($table, $data, $return=false) {
        $i = $this->table_identifier();
        $sql = "INSERT INTO {$i}$table{$i}";
        $cp = array();
        foreach($data as $field => $value) {
            if (is_numeric($value)) {
                $cp[$field] = "$value";
                continue;
            }
            $cp[$field] = "'{$value}'";
        }
        foreach($cp as $key => $value) {
            
        }
    }
}

class Query extends QueryShell {
}

class Result extends ResultShell {
    public function __construct($query) {
        $this->_result = $query;
    }

    public function status() {
    }

    public function count_rows_altered() {
    }
    public function get_rows_inserted() {
    }
    public function count_rows_inserted() {
    }
    public function has_next() {
    }
    public function get_next() {
    }
    public function get_all_assoc() {
    }
    public function get_all_obj() {
    }
}
