<?php
/**
 * Core PlumPHP Libary - PostgreSQL database library.
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
namespace Plum\DB\PostgreSQL;
use \Plum\DB\Connection as ConnectionShell;
use \Plum\DB\Result as ResultShell;
use \Plum\DB\Query as QueryShell;
use \Plum\DB\Table as TableShell;

class Connection extends ConnectionShell {
    public function connect(
        $user, $password, $database, $server = 'localhost',
        $port = 5432, $persistant = false
    ) {
        $connection_string = "dbname={$database} port={$port} host={$server} ";
        $connection_string .= "user={$user} password={$password}";
        $this->_connection = pg_connect($connection_string);
    }

    /**
     * Run some sql and take a crazy guess at what the output should be.
     * If we're not told what the type of query this is, we will process the 
     * output accordingly.
     *
     * @param string    $sql is a sql query.
     * @return \Plum\DB\Result
     */
    public function sql($sql) {
        $result = pg_query($this->_connection, $sql);
        if($result == false) {
            $error = pg_last_error();
            if(!$error) {
                return false;
            }
            throw new \Plum\Exception("PostgreSQL Query Error: {$error}\nSQL:{$sql}");
        }
        // Results are when data is returned and only when data is returned.
        // We want a bool "True" otherwise.
        $status = pg_result_status($result);
        if($status === 1) {
            return true;
        }
        return new Result($result);
    }

    public function table_identifier() {
        return '"';
    }

    public function process_input($var) {
        if (is_numeric($var)) {
            return "{$var}";
        }
        return "'".pg_escape_string($var)."'";
    }

    public function insert($table, $data, $return=false) {
        if(empty($data)) {
            return true;
        }
        $arrays_expected = false;
        $i = $this->table_identifier();
        $sql = "INSERT INTO {$i}$table{$i}\n";
        $cp = array();
        $row = array();
        $insert_info= $this->build_values($data);
        $sql .= '(' . implode(', ', $insert_info->fields) . ")\nVALUES\n";
        $sql .= implode(",\n", $insert_info->data);

        return $this->sql($sql);
    }

    private function build_values($array, $obj=null) {
        $string = '';
        if(!is_array($array)) {
            throw new Exception();
        }

        $row = array();
        $fields = array();
        foreach($array as $key => $value) {
            if(is_array($value)) {
                $obj = $this->build_values($value, $obj);
            } else {
                $fields[] = $key;
                $row[$key] = $this->process_input($value);
            }
        }

        if(empty($obj)) {
            $obj = new \Plum\stdClass;
            $obj->data = array();
            $obj->fields = $fields;
        }
        if(!empty($row)) {
            $obj->data[] = '(' . implode(', ', $row) . ')';
        }

        return $obj;
    }

    public function delete($table, $where=array(), $return=false) {
        $i = $this->table_identifier();
        $sql = "DELETE FROM {$i}$table{$i}\n";
        if(!empty($where)) {
            if(!is_array($where)) {
                throw new \Plum\ArrayExpectedException($where);
            }
            $sql .= "WHERE ";
            $where_array = array();
            foreach($where as $field => &$value) {
                $where_array[] = "{$i}$field{$i} = $value";
            }
            $sql .= implode(' AND ', $where_array);
        }
        if($return) {
            $sql .= "\nRETURNING *";
        }
        return $this->sql($sql);

    }

    public function select($table, $where=array(), $limit=0, $offset=0, $sort='') {
        $i = $this->table_identifier();
        $sql = "
            SELECT *
            FROM {$i}{$table}{$i}
        ";
        $sql .= $this->build_where($where);
        if(!empty($sort)) {
            $sql .= "ORDER BY $sort\n";
        }
        if($limit != 0) {
            $sql .= " LIMIT {$limit} OFFSET {$offset}";
        }
        return $this->sql($sql);
    }

    public function update($table, $data, $where, $return=false) {
        $i = $this->table_identifier();
        $sql = "UPDATE {$i}$table{$i} SET ";
        $tmpsql = '';
        $data = $this->insert_values_recurse($data); //Cleans and makes multi-dim arrays 1-d arrays.
        $set = array();
        foreach($data as $field => $value) {
            $set[] = "{$i}$field{$i} = $value";
        }
        $sql .= implode(', ', $set);
        $sql .= $this->build_where($where);
        return $this->sql($sql);
    }

    private function build_where($where = array()) {
        $i = $this->table_identifier();
        $sql = '';
        $where_sql = array();
        foreach($where as $field => $value) {
            $tmpsql = "{$i}$field{$i} ";
            if($value === null) {
                $tmpsql .= 'IS NULL ';
            } else {
                $tmpsql .= ' = ' . $this->process_input($value);
            }
            $where_sql[] = $tmpsql;
        }
        if(!empty($where_sql)) {
            $sql .= 'WHERE ' . implode(' AND ', $where_sql) . "\n";
        }
        return $sql;
    }

    private function insert_values_recurse($data) {
        $rd = array();
        foreach($data as $field => &$value) {
            $td = array();
            if(is_array($value)) {
                $rd = array_merge($rd, $this->insert_values_recurse($value));
            }
            $td[$field] = $this->process_input($value);
        }
        if(!empty($td)) {
            $rd[] = $td;
        }

        return $rd;
    }
}

class Result extends ResultShell {
    public function __construct($query) {
        $this->_result = $query;
        $this->_row_count = pg_num_rows($this->_result);
        $this->_row_affected = pg_affected_rows($this->_result);
        $this->_position = 0;
        pg_result_seek($this->_result, $this->_position);
    }

    /**
     * What kind of result did we get?
     * False for failure. Defined values will be returned when we get there.
     * TODO: Maybe get rid of this. Let's not be ignorant of what the resut is.
     *
     * @return bool (will become int.)
     */
    public function status() {
        if(!$this->success()) {
            return false;
        }
        return true; // Return special cases so we know what we got.

    }

    /**
     * This is a very generalized function. We don't care what kind of query 
     * happened but we want to say true if the database didn't carp and nothing 
     * returned indicates any error.
     *
     * @return bool
     */
    public function success() {
        return pg_result_error($this->_result) === false ? true : false;
    }

    public function count_rows_altered() {
        return $this->row_affected;
    }

    public function get_rows_inserted() {
        /**
         * TODO: Think up something for this.
         */
        return false;
    }

    public function count_rows_inserted() {
        return $this->row_affected;
    }

    public function count_rows_returned() {
        if(empty($this->_row_count)) {
            return false;
        }
        return $this->_row_count;
    }

    public function has_next() {
        if(!$return_count = $this->count_rows_returned()) {
            return false;
        }
        if($this->_position >= $return_count) {
            return false;
        }
        return true;
    }

    public function get_next($object = true) {
        if(!$this->has_next()) {
            return false;
        }
        $this->_position++;
        if($object) {
            return pg_fetch_object($this->_result);
        }
        return pg_fetch_assoc($this->_result);
    }

    public function get_all_assoc() {
        if(!$this->count_rows_returned()) {
            return false;
        }
        return pg_fetch_all($this->_result);
    }

    public function get_all_obj() {
        $objects = array();
        foreach($this->get_all_assoc() as $row) {
            $objects[] = (object)$row;
        }
        return $objects;
    }
}

class Table {

}
