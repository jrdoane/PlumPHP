<?php
/**
 * Core PlumPHP Library - Logger (for application event logging.)
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
 * The logger is the very first module to be loaded because it is used for 
 * everything else. A lot of special logic happens here so it is very important 
 * that the code here just works. A good example is outputting logging 
 * information to the database before the database module has been loaded.
 */
class Logger {
    /**
     * Log stuff to the PHP log.
     */
    public static function log_action($module, $desc, $data='') {
        $log = '['.date("c").'] '. $module . ': '.$desc;
        if(!empty($data)) {
            $log .= "\n".$data;
        }
        error_log($log);
    }
    /**
     * Writes data to the log.
     * Configuration dependant - PHP logging or a database table?
     */
    public static function trace() {
        $stack = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $stack_strings = array();
        foreach($stack as $call) {
            $string = '';
            $call = (object)$call;
            if($call->file) {
                $string .= "{$call->file}: ";
            }
            if($call->class) {
                $string .= "{$call->class}{$call->type}";
            }
            $string .= "{$call->function}():{$call->line}";
            $stack_strings[] = $string;
        }
        return $stack_strings;
    }
}
