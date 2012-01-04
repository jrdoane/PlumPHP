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
    public static function init() {
        session_start();
        $_SESSION['sessid'] = session_id();

        // From here on we need to check to see if we:
        // A: are connected to a database, the default is used for sessions.
        // B: has a session table to use (we haven't implemented DDL in the db layer.)
        // C: Has an existing session id record, if no create one and use the db.
    }
}
