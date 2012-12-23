<?php
/**
 * PlumPHP Database Configuration file.
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

/**
 * If no servers are set as default, we will take the last one we encounter.
 * We don't want to handle this well because this is highly discouraged.
 */
$config['servers'] = array(
    'default' => array(
        'dbtype' => 'PostgreSQL',
        'server' => 'localhost',
        'port' => '5432',
        'username' => 'user',
        'password' => 'password',
        'database' => 'database',
        'prefix' => '',
        'persistant' => true,
        'default' => true
    )
);
