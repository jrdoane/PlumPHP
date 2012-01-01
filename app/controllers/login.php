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
class Login extends \Plum\Controller {
    public function index() {
        /**
         * Generate the page and handle.
         */
        $html = \Plum\HtmlBuilder('div', array('class' => 'loginform'));
        $html->form(
            array(
                'action' => \Plum\Uri::href('login'),
                'method' => 'POST'
            )
        );

        /**
         * Output the page
         */
        $page = new stdClass;
        \Plum\View::load('page', array('page' => $page));
    }
}
