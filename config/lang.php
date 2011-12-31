<?php
/**
 * PlumPHP system configuration settings
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

$plumdir = dirname(dirname(__FILE__));

// What language should we default to?
$config['lang'] = 'en_utf8';
$config['default_file'] = 'default';

// Where are our language search directories?
$config['app_dirs'] = array (
    "$plumdir/app/lang"
);

$config['plum_dirs'] = array (
    "$plumdir/plum/lang"
);

