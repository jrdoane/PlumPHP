<?php
namespace Plum;

class HTTP {
    public static function send_404() {
        header('HTTP/1.0 404 Not Found', false, 404);
    }
}
