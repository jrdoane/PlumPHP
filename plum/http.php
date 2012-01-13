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

    public static function redirect($url) {
        // This is special. Since there might be components in plum that need to 
        // shutdown properly, we're going to call init to shut things down. This 
        // is particularly important if we want to save the state of the session 
        // in the database.
        Init::system_shutdown();
        header("Location: {$url}");
        exit();
    }

    public static function input($name, $type=\Plum\PARAM_TEXT, $from=\Plum\FROM_REQUEST) {
        $locations = array();
        if(empty($from)) {
            $from = \Plum\FROM_REQUEST;
        }

        switch($from) {
        case \Plum\FROM_REQUEST:
            $input = !empty($_REQUEST[$name]) ? $_REQUEST[$name] : '';
            break;
        case \Plum\FROM_POST:
            $input = !empty($_POST[$name]) ? $_POST[$name] : '';
            break;
        case \Plum\FROM_FILE:
            $input = !empty($_FILES[$name]) ? $_FILES[$name] : '';
            break;
        case \Plum\FROM_COOKIE:
            $input = !empty($_COOKIE[$name]) ? $_COOKIE[$name] : '';
            break;
        default:
            throw new UnknownParamTypeException($from);
        }

        if(!empty($input)) {
            return self::clean($input, $type);
        }
        return false;
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
