<?php
namespace Plum;

/**
 * The logger is the very first module to be loaded because it is used for 
 * everything else. A lot of special logic happens here so it is very important 
 * that the code here just works. A good example is outputting logging 
 * information to the database before the database module has been loaded.
 */
class Logger {
    static protected $db_backlog;

    /**
     * Logger will contain various methods that relate to a specific log level. 
     * This way if the logger is configured to ignore developer log calls but 
     * not warnings, then it should handle it as such.
     */
    public static function debug($message, $data) {
    }

    public static function warning($message, $data) {
    }

    public static function error($message, $data) {
    }

    public static function trace($from) {
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
    }
}
