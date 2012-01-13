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
$config['application_dir'] = 'app';
$config['controller_dir'] = 'controllers';

$config['action_prefix'] = ''; // adding foo_ here would make default foo_index.
$config['action_suffix'] = ''; // adding _foo here would make default index_foo.
$config['controller_class_prefix'] = '';
$config['controller_class_suffix'] = '';

$config['debug'] = true;

/**
 * Add non-core modules here.
 */
$config['extensions'] = array (
    'Auth'
);
