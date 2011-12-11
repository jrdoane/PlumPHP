<?php
class Welcome extends \Plum\Controller {
    public function index() {
        $x = array(1,2,3,4,5,6,7,
            'apples' => array(
                1,2,3,4,5,6,7, array(
                    1,2,3,4,5,6,7,8,9,10
                )
            )
        );
        $test = \Plum\Debug::out($x);
        \Plum\View::load('tpl/small', array(
            'titlebar' => 'PlumPHP',
            'center' => $test,
            'breadcrumbs' => 'PlumPHP'
            )
        );
    }

    public function test_db() {
        $build = \Plum\Html::builder();
        $database = \Plum\DB::get_conn(); // Gets default connection
        $result = $database->select('test');

        $build->tag('body', array(), '', true);
        $build->pre(var_export($result->get_all_obj(), true));

        print $build->get_string();
    }

    public function sql($sql) {
        if(empty($sql)) {
            print "SQL required.";
            return;
        }
        $build = \Plum\Html::builder();
        $result = \Plum\DB::exec_conn($sql);
        foreach($result->get_all_obj() as $row) {
            $build->p(var_export($row, true));
        }
        print $build->get_string();
    }
}
