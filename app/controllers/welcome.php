<?php
/**
 * PlumPHP Welcome Controller
 *
 * PlumPHP is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PlumPHP is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PlumPHP.  If not, see <http://www.gnu.org/licenses/>.
 */
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
