<?php

/**
 * Recent updates view.
 *
 * @category   apps
 * @package    software-updates
 * @subpackage views
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2012 ClearFoundation
 * @copyright  2012 Tim Burgess
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/software_updates/
 */

///////////////////////////////////////////////////////////////////////////////
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.  
//
///////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////
// Load dependencies
///////////////////////////////////////////////////////////////////////////////

$this->lang->load('base');
$this->lang->load('software_updates');

$headers = array(
    lang('software_updates_package'),
    lang('base_action'),
    lang('base_date') . '/' . lang('base_time')
);

$rows = array();

foreach ($log as $logentry) {
    $row = array();
    $package_name = substr($logentry['package'], 0, strrpos($logentry['package'], '.'));

    // TODO: see tracker 4371
    if (strlen($package_name) > 40)
        $package_name = substr($package_name, 0, 40) . ' ...';

    $row['details'] = array(
        $package_name,
        $logentry['action'],
        $logentry['date'] . ', ' . $logentry['time']
    );
    $rows[] = $row;
}

$anchors = array();

///////////////////////////////////////////////////////////////////////////////
// Table
///////////////////////////////////////////////////////////////////////////////

$options = array(
    'id' => 'activity_list',
    'sort-default-col' => 2,
    'sort-default-dir' => 'desc',
    'no_action' => TRUE
);

echo summary_table(
    lang('software_updates_recent_software_activity'),
    $anchors,
    $headers,
    $rows,
    $options
);
