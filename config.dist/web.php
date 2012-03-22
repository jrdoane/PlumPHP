<?php
/**
 * Default core config file.
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

// Just start assigning stuff to $config. It is an assoc array.

$config['wwwroot'] = 'http://yourwebsite.com/';
$config['wwwfile'] = 'index.php'; // (With a properly configured .htaccess file, this can be ''

$config['default_controller'] = 'welcome'; // Controller to default to if uri string is empty.
$config['default_method'] = 'index'; // This is the default method that gets call if the uri string doesn't have a method.

$config['site_name'] = 'Your websites full name here.';
$config['site_name_short'] = 'Short name here.';

$config['dbsession'] = false; // Are you going to be using a database?
$config['session_timeout'] = 3600; // 1 Hour

