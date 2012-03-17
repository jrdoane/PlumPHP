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

    public static function login($username, $password, $test=false) {
        $db = DB::get_conn();
        if(!$user = $db->select('user', array('username' => $username), 1)) {
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

        // We just wanted to see if the user/password pair was valid.
        if($test) {
            return true;
        }

        // Log the user in.
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
    
    public static function is_privileged($privilege, $user=null) {
        if($user === null) {
            if(!self::is_logged_in()) {
                return false;
            }
            $user = self::$_user->user_id;
        }
        if(is_object($user) & !empty($user->user_id)) {
            $user = $user->user_id;
        }

        if(!is_numeric($user)) {
            throw new InvalidParameterTypeException($user);
        }

        if(!is_string($privilege)) {
            throw new InvalidParameterTypeException($privilege);
        }

        $db = DB::get_conn();
        $prefix = $db->get_prefix();
        $sql = "
            SELECT r.*, p.*
            FROM {$prefix}user AS u
            JOIN {$prefix}user_role AS ur USING (user_id)
            JOIN {$prefix}role AS r USING (role_id)
            LEFT JOIN {$prefix}role_privilege AS rp USING (role_id)
            LEFT JOIN {$prefix}privilege AS p USING (privilege_id)
            WHERE u.user_id = {$user} AND
            (r.super_user = 1 OR p.name = '{$privilege}')
            ";
        $result = $db->sql($sql, true);
        if($result->has_next()) {
            return true;
        }
        return false;
    }

    public static function grant_role($role_id, $user_id) {
        $db = DB::get_conn();
        $db->insert('user_role', array('role_id' => $role_id, 'user_id' => $user_id));
    }

    public static function revoke_role($role_id, $user_id) {
        $db = $DB::get_conn();
        $db->delete('user_role', array('role_id' => $role_id, 'user_id' => $user_id));
    }
}
