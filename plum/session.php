<?php
/**
 * Authentication Library - PlumPHP Extension
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
namespace Plum;

class CookiesUnsupportedException extends Exception {}

/**
 * Authentication class using plum-level controls.
 */
class Session {
    protected static $_use_database;
    protected static $_stored_session; // Database session copy.
    protected static $_session;
    protected static $_dirty;
    protected static $_expires_on;

    public static function init() {
        session_start();

        // Use cookie sessions if there is no database.
        self::$_use_database = Config::get('dbsession', 'web');

        if(self::$_use_database == true) {
            self::$_stored_session = false;
            self::$_session = array();
            self::set_time_expires(time() + Config::get('session_timeout', 'web'));
            $conn = DB::get_conn();
            self::purge_sessions();

            $cname = Config::get('cookie_session_name', 'web');

            if(isset($_COOKIE[$cname])) {
                $id = $_COOKIE[$cname];
            } else {
                $id = null;
            }

            if(is_numeric($id)) {
                $session = $conn->select('session', array('session_id' => $id), 1);
                if(self::validate_session($session)) {
                    self::$_session = json_decode($session->data, true);
                    if(!empty(self::$_session['obj_names'])) {
                        foreach(self::$_session as $sin => &$si) {
                            if(in_array($sin, self::$_session['obj_names'])) {
                                $si = (object)$si;
                            }
                        }
                    }
                    self::$_stored_session = $session;
                    self::update_cookie();
                    return;
                }
            }

            self::save_session();
            self::update_cookie();

        } else {
            // Grab data from the php session.
            // PHP handles this timeout and all that lovely jazz.
            self::$_session =& $_SESSION;
        }
    }

    /**
     * Is this a valid session? From date and timeout to nulls!
     *
     * @param mixed     $session Something that could be a session.
     * @return bool
     */
    public static function validate_session($session) {
        // HERE WE GO!
        if(empty($session)) {
            return false;
        }
        if(!is_object($session)) {
            return false;
        }
        $max_age = time() - Config::get('session_timeout', 'web');
        if($session->time_modified < $max_age) {
            return false;
        }

        $client_agent = $_SERVER['HTTP_USER_AGENT'];
        if($client_agent != $session->agent) {
            return false;
        }

        return true;
    }

    /**
     * Delete all old sessions from the database.
     */
    public static function purge_sessions() {
        $db = DB::get_conn();
        if(self::$_use_database == false) {
            return;
        }
        $timeout = Config::get('session_timeout', 'web', 3600);
        $short_timeout = Config::get('session_short_timeout', 'web', 600);
        $now = time();
        return $db->sql("
            DELETE FROM {session}
            WHERE (
                (time_modified + {$timeout}) < ?
            ) OR (
                time_modified = time_created AND
                time_created + {$short_timeout} < ?
            )
            ", array($now, $now)
        );
    }

    /**
     * Gets a session variable.
     * Returns null if there is no variable.
     *
     * @param string    $name is a session variable to get.
     * @return mixed.
     */
    public static function get($name) {
        if(!isset(self::$_session[$name])) {
            return null;
        }
        return self::$_session[$name];
    }

    /**
     * Sets a session variable.
     *
     * @param string    $name of the session variable.
     * @param mixed     $value is what gets put into the session var.
     * @return null
     */
    public static function set($name, $value) {
        self::$_dirty = true;
        self::$_session[$name] = $value;
    }

    /**
     * Removes a session variable.
     *
     * @param string    $name is the session var name.
     * @return null
     */
    public static function nullify($name) {
        if(isset(self::$_session[$name])) {
            self::$_dirty = true;
            unset(self::$_session[$name]);
        }
    }

    /**
     * Invalidates the session and creates a new one.
     */
    public static function reset() {
        if(!self::$_use_database) {
            session_destroy();
        } else {
            $conn = DB::get_conn();
            $oldsessid = self::current_id();
            $conn->delete('session', array('session_id' => $oldsessid));
            self::$_session = array();
            self::$_stored_session = false;
            self::save_session();
            self::update_cookie();
        }
        self::$_session = array();
        self::$_stored_session = false;
    }

    /**
     * Update cookie stores the session_id that's in the database into a cookie 
     * so we can look it up the next time the system loads.
     *
     * @return null
     */
    public static function update_cookie() {
        $db = DB::get_conn();
        $session_id = self::current_id();
        $domain = Config::get('cookie_domain', 'web');
        $cname = Config::get('cookie_session_name', 'web');

        if(!self::$_stored_session or !$session_id) {
            throw new Exception('Unable to update cookie. No session id exists.');
        }

        $_COOKIE[$cname] = $session_id;
        setcookie($cname, $session_id, self::$_expires_on, '/', $domain);
        self::set_cookie_checker();
    }

    public static function get_cookie_checker_name() {
        return Config::get('cookie_session_name', 'web') . '_checker';
    }

    public static function set_cookie_checker() {
        $domain = Config::get('cookie_domain', 'web');
        setcookie(self::get_cookie_checker_name(), 1, self::$_expires_on, '/', $domain);
    }

    public static function require_cookies() {
        if(isset($_COOKIE[self::get_cookie_checker_name()])) {
            return true;
        }
        throw new CookiesUnsupportedException();
    }

    public static function set_time_expires($time) {
        self::$_expires_on = $time;
    }

    public static function current_id() {
        if(self::$_stored_session) {
            return self::$_stored_session->session_id;
        }
        return false;
    }

    public static function shutdown() {
        self::save_session();
    }

    public static function save_session() {
        $objs = array();
        if(!self::$_use_database) {
            return;
        }
        foreach(self::$_session as $key => &$so) {
            if(is_object($so)) {
                $objs[] = $key;
                $so = get_object_vars($so);
            }
        }

        self::$_session['obj_names'] = $objs;
        $db = DB::get_conn();
        if(empty(self::$_stored_session)) {
            $session = (object)array(
                'ip' => $_SERVER['REMOTE_ADDR'],
                'agent' => $_SERVER['HTTP_USER_AGENT'],
                'data' => json_encode(self::$_session),
                'time_created' => time(),
                'time_modified' => time()
            );
            $r = $db->insert('session', $session, true);
        } else {
            $where = array(
                'session_id' => self::current_id()
            );
            $session = (object)array(
                'data' => json_encode(self::$_session),
                'time_modified' => time()
            );

            $r = $db->update('session', $session, $where, true);
            if(!empty($r)) {
                $r = array_pop($r);
            }
        }

        if(!$r) {
            throw new Exception('Unable to store the session.');
        }

        self::$_stored_session = $r;
    }
}
