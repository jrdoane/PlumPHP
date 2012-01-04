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
        $username = \Plum\HTTP::input('username');
        $password = \Plum\HTTP::input('password');

        if(!empty($username) and !empty($password)) {
            print_r($username);
            print_r($password);
            die();
        }
        /**
         * Generate the page and handle.
         */
        $html = new \Plum\HtmlBuilder();
        $html->div(array('class' => 'loginform'))
            ->form(
            array(
                'action' => \Plum\Uri::href('login'),
                'method' => 'POST'
            )
        );

        $html->h(2, \Plum\Lang::get('login'))
            ->label(\Plum\Lang::get('username'), 'username')
            ->input('text', 'username')
            ->br()
            ->label(\Plum\Lang::get('password'), 'password')
            ->input('password', 'password')
            ->br()
            ->input('submit', array('value' => \Plum\Lang::get('login')))
            ->step_out('div');

        /**
         * Output the page
         */
        $page = new stdClass;
        $page->body = $html;
        \Plum\View::load('page', array('page' => $page));
    }
}
