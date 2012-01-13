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
class Auth {
    protected static $_user;
    protected static $_impersonate;

    public static function init() {
        self::$_user = null;
        self::$_impersonate = null;

        if($user = Session::get('user')) {
            self::$_user = $user;
        }
    }

    public static function logout() {
        self::$_user = null;
        self::$_impersonate = null;
    }

    public static function login($username, $password) {
        $db = DB::get_conn();
        $result = $db->select('user', array('username' => $username), 1);
        if(!$user = $result->get_next(true)) {
            // User does not exist.
            return false;
        }

        switch($user->auth) {
            case 'md5':
            case 'sha1':
                $hp = call_user_func($user->auth, $password);
                if($user->password != $hp) {
                    // Password is incorrect.
                    return false;
                }
                break;
            default:
                // No auth defined, which means no login.
                return false;
        }

        $user->last_login = time();
        $db->update('user', array('last_login' => time()), array('user_id' => $user->user_id));
        self::$_user = $user;
        return true;
    }

    public static function impersonate($user) {
        self::$_impersonate = self::$_user;
        self::$_user = $user;
    }

    public static function restore_login() {
        self::$_user = self::$_impersonate;
        self::$_impersonate = null;
    }

    public static function get_current_user() {
        return self::$_user;
    }

    public static function is_logged_in() {
        return self::$_user != null;
    }

    public static function shutdown() {
        Session::set('user', self::$_user);
    }
}
