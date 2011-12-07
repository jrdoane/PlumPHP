<?php
namespace Plum;

/**
 * Module exception definitions.
 */
class MissingParamTypeException extends \Plum\ParameterException {}
class UnknownParamTypeException extends \Plum\ParameterException {}

/**
 * Module definition class. (Init uses it.)
 */
class HTTP {
    public static function send_404() {
        header('HTTP/1.0 404 Not Found', false, 404);
    }

    public static function input($name, $type=PARAM_TEXT, $from=FROM_REQUEST) {
        $locations = array();
        if(empty($from)) {
            $from = FROM_REQUEST;
        }
        switch($from) {
        case FROM_REQUEST:
            if(!empty($_REQUEST[$name])) {
                return self::clean($_REQUEST[$name], $type);
            }
        case FROM_POST:
            if(!empty($_POST[$name])) {
                return self::clean($_REQUEST[$name], $type);
            }
        default:
            throw new UnknownParamTypeException();
        }
    }

    public static function clean($data, $param) {
        $sregex = '';
        switch($param) {
        case PARAM_INT:
            // Int, strip everything except integers.
            $data = preg_replace('/[^0-9]/', '', $data);
            break;
        case PARAM_FLOAT:
            $data = preg_replace('/^(+[0-9]\.+[0-9])/', '', $data);
            break;
        case PARAM_BOOL:
            if(preg_match('/(false|null|0)/i', $data)) {
                $data = false;
            }
            $data = true;
            break;
        case PARAM_TEXT:
        case PARAM_RAW:
            break;
        default:
            
        }
        return $data;
    }
}
