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
    lang('base_date'),
    lang('base_time'),
    lang('base_action'),
    lang('software_updates_package')
);

$rows = array();

foreach ($log as $logentry)
{
    $row['details'] = array($logentry['date'], $logentry['time'], $logentry['action'], $logentry['package']);
    $rows[] = $row;
}

$anchors = array();

///////////////////////////////////////////////////////////////////////////////
// Table
///////////////////////////////////////////////////////////////////////////////

$options['no_action'] = TRUE;

echo summary_table(
     lang('software_updates_recent_software_activity'),
     $anchors,
     $headers,
     $rows,
     $options
);
