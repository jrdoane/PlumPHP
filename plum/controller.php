<?php
namespace plum;

/**
 * What a controller does. You always need to inherit this.
 */

abstract class Controller {
    /**
     * Controller variables.
     */
    protected $_page;

    /**
     * Methods required by all controllers but start here.
     */
    public function __construct() {
        $this->_page = new stdClass;
    }

    /**
     * Functions used by the controller but not required.
     */
    public function before();
    public function after();
}
?>
