<?php
/**
 * PlumPHP CIM Controller
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
class CIM extends \Plum\Controller {
    public function before() {
    }

    private function get_evr_xml() {
        $response_attr = array('impl' => 'evr', 'version' => '1.0');
        return new \Plum\XmlBuilder('response', $response_attr);
    }
    
    public function info() {
        $xml = $this->get_evr_xml();
        $xml->tag('servername', array(), 'PlumPortal');
        $xml->tag('version', array('numeric' => 20110219), '1.0');
        $xml->tag('auth', array(), \Plum\Uri::href('cim/auth'));
        print $xml->get_string();
    }

    public function index() {
        $xml = $this->get_evr_xml();
        $xml->tag('redirect', array(), \Plum\Uri::href('cim/auth'));
        $xml->tag('verbose', array('ignore' => 'true'), 'Auth required, redirecting to auth.');
        print $xml->get_string();
    }

    public function auth($username = null, $password = null) {
        $xml = $this->get_evr_xml();
        if(!empty($username) and !empty($password)) {
            if(\Plum\Auth::login($username, $password, true)) {
                $xml->tag('status', array(), 'success');
                $xml->tag('token', array(), md5(microtime(true)));
                $zone = date_default_timezone_get();
                $xml->tag('time_issued', array('zone' => $zone, 'type' => 'unix'), time());
            } else {
                $xml->tag('status', array(), 'failed');
                $xml->tag('reason', array(), 'Username password pair incorrect.');
            }
        } else {
            $xml->tag('function', array(), __FUNCTION__);
            $xml->tag('status', array(), 'desc');
            $xml->tag('params', array(), '', true);
            $xml->tag('get', array('id' => '1', 'required' => true), 'username');
            $xml->tag('get', array('id' => '2', 'required' => true), 'password');
            $xml->step_out();
            $xml->tag('return', array('name' => 'token', 'type' => 'string'));
            $xml->tag('return', array('name' => 'time_issued', 'type' => 'int'));
        }
        print $xml->get_string();
    }

    /**
     * Invalidate an authentication token.
     * TODO: Ban users where repeated attempts fail.
     *
     * @param string    $token is an auth token to invalidate.
     */
    public function unauth($token) {
        $xml = $this->get_evr_xml();
        $xml->tag('status', array(), 'success');
        print $xml->get_string();
    }

    /**
     * Gets a list of friends for a user. This will also provide user states.
     *
     * @param string    $token is a hexadecimal hash representing an authenticated user.
     */ 
    public function friends($token) {
        $xml = $this->get_evr_xml();
        $xml->tag('status', array(), 'success');
        $xml->tag('friends', array(), '', true);
        $xml->tag('friend', array(), 'someperson');
        $xml->tag('friend', array(), 'jdoane');
        $xml->tag('friend', array(), 'moquist');
        $xml->step_out();
        print $xml->get_string();
    }

    /**
     * Send a message as an authenticated user.
     *
     * @param string    $token is a hexadecimal hash representing an authenticated user.
     * @param string    $to is a username.
     * @param string    $message is a message to send to a user.
     */
    public function send($token, $to, $message) {
    }

    /**
     * Gets all updates for a given login. This will get called regularly so it 
     * needs to be fast and efficent.
     *
     * @param string    $token is a hexadecimal hash representing an authenticated user.
     * @param int       $lastupdate will let a client pull all changes from 
     *                  a given time forward. ** TODO: Make this smart ***
     */
    public function query($token, $lastupdate = null) {
    }
}
