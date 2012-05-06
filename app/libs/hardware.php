<?php

class HardwareForm extends \Plum\Form {

    /**
     * Assemble creates the form.
     */
    public function assemble() {
        $this->add('text', array('name' => 'name'), \Plum\Lang::get('hardwarename', 'hardware'));
        $this->add('text', array('name' => 'serialnumber'), \Plum\Lang::get('serialnumber', 'hardware'));
        $this->add('text', array('name' => 'idnumber'), \Plum\Lang::get('idnumber', 'hardware'));
        $this->add('textarea', array('name' => 'notes'), \Plum\Lang::get('notes', 'hardware'));
        $this->add('submit', array('value' => \Plum\Lang::get('savehardware', 'hardware')));
    }
}

class HardwareGroupForm extends \Plum\Form {

    public function assemble() {
    }
}

