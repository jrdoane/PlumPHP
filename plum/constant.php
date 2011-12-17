<?php
/**
 * Core PlumPHP library file - Constants
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

define('Plum\PARAM_INT',     0x1);
define('Plum\PARAM_FLOAT',   0x2);
define('Plum\PARAM_TEXT',    0x3);
define('Plum\PARAM_RAW',     0x4);
define('Plum\PARAM_BOOL',    0x5);

/** Set as powers of 2 so methods can be bitwise added. **/
/** There is also NO GET. Plum takes exclusive control of GET. **/
define('Plum\FROM_REQUEST', 0x0);
define('Plum\FROM_POST',    0x1);
define('Plum\FROM_COOKIE',  0x2);
define('Plum\FROM_FILE',    0x3);
