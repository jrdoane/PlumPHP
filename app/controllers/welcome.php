<?php
class Welcome extends \Plum\Controller {
    public function index() {
        \Plum\View::load('welcome', array('test' => '<p>Hello World</p>'));
    }
}
