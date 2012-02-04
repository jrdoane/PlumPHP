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

/**
 * Authentication class using plum-level controls.
 */
class Session {
    protected static $_use_database;
    protected static $_stored_session; // Database session copy.
    protected static $_session;
    protected static $_dirty;

    public static function init() {
        // We only need the session to maintain a session id.
        // If there is no database access, it will fall back to the php session.
        // TODO: Add config setting to switch between the two.
        session_start();

        // From here on we need to check to see if we:
        // A: are connected to a database, the default is used for sessions.
        // B: has a session table to use (we haven't implemented DDL in the db layer.)
        // C: Has an existing session id record, if no create one and use the db.
        $conn = DB::get_conn();

        // Use cookie sessions if there is no database.
        self::$_use_database = Config::get('dbsession', 'web');

        if(self::$_use_database == true) {
            // Grab data from the database.
            $session = $conn->select('session', array('sessid' => session_id()), 1);
            $stimeout = Config::get('session_timeout', 'web');

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
            } else {
                if(!empty($session)) {
                    $conn->delete('session', array('sessid' => session_id()));
                }
                self::$_session = array();
                self::$_stored_session = false;
            }
        } else {
            // Grab data from the php session (cookie.)
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
        if(empty($session->time_modified)) {
            return false;
        }
        if(!is_numeric($session->time_modified)) {
            return false;
        }
        $max_age = time() - Config::get('session_timeout', 'web');
        if($session->time_modified < $max_age) {
            return false;
        }
        return true;
    }

    // TODO
    public static function clean_sessions() {
    }

    // TODO
    public static function purge_sessions() {
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
     * Invalidates the session and creates a new one.
     */
    public static function reset() {
        $oldsessid = session_id();
        $conn = DB::get_conn();
        $conn->delete('session', array('sessid' => $oldsessid));
        self::$_session = array();
        self::$_stored_session = false;
        session_regenerate_id(true);
    }

    public static function shutdown() {
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
                'sessid' => session_id(),
                'ip' => $_SERVER['REMOTE_ADDR'],
                'agent' => $_SERVER['HTTP_USER_AGENT'],
                'data' => json_encode(self::$_session),
                'time_created' => time(),
                'time_modified' => time()
            );
            $db->insert('session', $session);
        } else {
            // TODO: Add config checks for agent and ip.
            $where = array(
                'sessid' => session_id()
            );
            $session = (object)array(
                'data' => json_encode(self::$_session),
                'time_modified' => time()
            );

            $r = $db->update('session', $session, $where);
        }
    }
}
