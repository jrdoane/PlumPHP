<?php
class Welcome extends \Plum\Controller {
    public function index() {
        $x = array(1,2,3,4,5,6,7);
        $test = \Plum\Debug::out($x);
        \Plum\View::load('welcome', array('test' => $test));
    }
}
