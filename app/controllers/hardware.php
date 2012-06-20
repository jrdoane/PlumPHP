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

require_once(dirname(dirname(__FILE__)) . '/libs/hardware.php');
require_once(dirname(dirname(__FILE__)) . '/libs/lib.php');

class Hardware extends \Plum\Controller {
    protected $_page;

    public function before() {
        if(!\Plum\Auth::is_logged_in()) {
            \Plum\Http::redirect(\Plum\Uri::href('login'));
        }
        $this->_page = new stdClass;
        $page =& $this->_page;
        $page->styles = array('basic');
        $page->breadcrumbs = array (
            array (
                'text' => \Plum\Lang::get('hardwaremanager', 'hardware'),
                'url' => \Plum\Uri::href('hardware')
            )
        );
    }

    public function index() {
        $page =& $this->_page;
        $column = \Plum\View::load('hardware/navigation');
        $content = new \Plum\HtmlBuilder();
        $content->h(1, \Plum\Lang::get('hardwaremanager', 'hardware'))
            ->p("TODO: Put more stuff here.");
        $page->body = \Plum\View::load('onecolumn', array('content' => $content, 'column' => $column));
        \Plum\View::load('page', array('page' => $page));
    }

    public function additem() {
        // We could have submitted already, lets check!
        $form = new HardwareForm(
            \Plum\Uri::href('hardware/additem'), 'POST',
            \Plum\Lang::get('addhardware', 'hardware')
        );

        if($data = $form->get_valid_data()) {
            $user = \Plum\Auth::get_current_user();
            $row = (object)array(
                'name' => $data->name,
                'serial_number' => $data->serialnumber,
                'id_number' => $data->idnumber,
                'notes' => $data->notes,
                'created_by' => $user->user_id,
                'modified_by' => $user->user_id,
                'time_created' => time(),
                'time_modified' => time()
            );
            $db = \Plum\DB::get_conn();
            $db->insert('hardware', $row);
            \Plum\HTTP::redirect(\Plum\Uri::href('hardware/manageitems'));
        }

        // Start making the page.
        $page =& $this->_page;
        $page->rtf = true;
        $page->breadcrumbs[] = array(
            'text' => \Plum\Lang::get('addhardware', 'hardware')
        );
        $column = \Plum\View::load('hardware/navigation');
        $content = new \Plum\HtmlBuilder();
        $content->merge_builders($form->get_builder(), true);
        $body = \Plum\View::load(
            'onecolumn', array(
                'content' => $content,
                'column' => $column
            )
        );
        $page->body = $body;
        \Plum\View::load('page', array('page' => $page));
    }

    public function managegroups() {
        $page =& $this->_page;
    }

    public function manageitems() {
        $page =& $this->_page;
        $page->breadcrumbs[] = array(
            'text' => \Plum\Lang::get('managehardware', 'hardware')
        );
        $column = \Plum\View::load('hardware/navigation');
        // Get hardware and plug it into the hardware manager view. This should 
        // be generic enoug where items and groups both can use the same view.
        $db = \Plum\DB::get_conn();

        $records = $db->select('hardware', array(), 10, 0);

        // Lets build a some html, just some, the table.
        $content = new \Plum\HtmlBuilder();
        if($records) {
            // Records found.
            $content->table()
                ->tr(array('class' => 'head'))
                ->th(\Plum\Lang::get('hardwarename', 'hardware'))
                ->th(\Plum\Lang::get('serialnumber', 'hardware'))
                ->th(\Plum\Lang::get('idnumber', 'hardware'))
                ->step_out('table');
            foreach($records as $rec) {
                $content->tr(array('class' => 'item'))
                    ->td()
                    ->a($rec->name, \Plum\Uri::href("hardware/viewitem/{$rec->hardware_id}"))
                    ->step_out('tr')
                    ->td($rec->serial_number)
                    ->td($rec->id_number)
                    ->step_out('table');
            }

        } else {
            // No records found.
                $content->h(1, \Plum\Lang::get('managehardware', 'hardware'));
                $content->h(3, \Plum\Lang::get('nohardwaretodisplay', 'hardware'));
        }

        $page->body = \Plum\View::load(
            'onecolumn', array(
                'content' => $content,
                'column' => $column
            )
        );

        \Plum\View::load('page', array('page' => $page));
    }

    public function viewitem($item_id) {
        $page =& $this->_page;
        $page->breadcrumbs[] = array(
            'text' => \Plum\Lang::get('managehardware', 'hardware'),
            'url' => \Plum\Uri::href('hardware/manageitems')
        );
        $page->breadcrumbs[] = array(
            'text' => \Plum\Lang::get('hardwaredescription', 'hardware')
        );
        $db = \Plum\DB::get_conn();
        $rec = $db->select('hardware', array('hardware_id' => $item_id), 1);

        $html = new \Plum\HtmlBuilder();
        if(!$rec) {
            // No record found.
        } else {
            $html->table()
                ->tr()
                ->th(\Plum\Lang::get('hardwarename', 'hardware'))
                ->td($rec->name)
                ->step_out('table')
                ->tr()
                ->th(\Plum\Lang::get('addedby', 'hardware'))
                ->td(fullname($rec->created_by))
                ->step_out('table')
                ->tr()
                ->th(\Plum\Lang::get('serialnumber', 'hardware'))
                ->td($rec->serial_number)
                ->step_out('table')
                ->tr()
                ->th(\Plum\Lang::get('idnumber', 'hardware'))
                ->td($rec->id_number)
                ->step_out('table')
                ->tr()
                ->th(\Plum\Lang::get('notes', 'hardware'))
                ->raw('td', array(), $rec->notes)
                ->step_out('table');
            // Lets display the ticket.
        }

        $column = \Plum\View::load('hardware/navigation');
        $page->body = \Plum\View::load(
            'onecolumn', array(
                'content' => $html,
                'column' => $column
            )
        );

        \Plum\View::load('page', array('page' => $page));
    }
}
