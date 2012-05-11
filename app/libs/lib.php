<?php
/**
 * PlumPHP Portal Primary Application Library
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

function fullname($user) {
    if(is_numeric($user)) {
        $user = get_user($user);
    }
    return $user->firstname . ' ' . $user->lastname;
}

function get_user($user_id) {
    $db = \Plum\DB::get_conn();
    return $db->select('user', array('user_id' => $user_id), 1);
}
