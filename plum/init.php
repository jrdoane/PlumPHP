<?php
// Little helper class.
class Init {
    public static function core($module) {
        require_once("{$module}.php");
    }
}

// Starts up Plum.
Init::core('config');
Init::core('uri');
Init::core('controller');
