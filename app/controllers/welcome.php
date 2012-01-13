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
    public function before() {
        if(!\Plum\Auth::is_logged_in()) {
            \Plum\Http::redirect(\Plum\Uri::href('login'));
        }
    }

    public function index() {
        $page = new stdClass;
        $page->breadcrumbs = array (
            array (
                'text' => 'Test Crumb 1',
                'url' => \Plum\Uri::href()
            ),
            array (
                'text' => 'Test Crumb 2',
                'url' => \Plum\Uri::href()
            ),
        );
        \Plum\View::load('page', array('page' => $page));
    }
}
