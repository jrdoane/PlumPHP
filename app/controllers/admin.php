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
require_once(dirname(dirname(__FILE__)) . '/libs/admin/lib.php');

class Admin extends \Plum\Controller {
    protected $_page;

    public function before() {
        if(!\Plum\Auth::is_logged_in()) {
            \Plum\Http::redirect(\Plum\Uri::href('login'));
        }
        $this->_page = new stdClass;
        $page =& $this->_page;
        $page->styles = array('basic', 'admin');
        $page->breadcrumbs = array (
            array (
                'text' => \Plum\Lang::get('administration'),
                'url' => \Plum\Uri::href('admin')
            )
        );

    }

    public function index() {
        $page =& $this->_page;
        $page->body = \Plum\View::load('admin');
        \Plum\View::load('page', array('page' => $page));
    }

    public function motd() {
        $page =& $this->_page;
        $page->breadcrumbs[] = array(
            'text' => \Plum\Lang::get('editmotd', 'admin')
        );
        $page->rtf = true;
        $form = new Motd_Form(\Plum\Uri::href('admin/motd'), 'POST', \Plum\Lang::get('motd'));
        $page->body = $form->get_builder();
        \Plum\View::load('page', array('page' => $page));
    }

    private function nav_column($current) {
    }

    private function main_column($content) {
    }
}
