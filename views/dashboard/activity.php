<?php

/**
 * Recent activity dashboard view.
 *
 * @category   apps
 * @package    software-updates
 * @subpackage views
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2012-2015 ClearFoundation
 * @copyright  2012-2015 Tim Burgess
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

///////////////////////////////////////////////////////////////////////////////
// Anchor
///////////////////////////////////////////////////////////////////////////////

$buttons = array(
    anchor_custom("/app/software_updates", lang('base_view'), 'high')
);
$headers = array(
    lang('software_updates_package'),
    lang('base_action'),
    lang('base_date') . '/' . lang('base_time')
);

$rows = array();

foreach ($log as $logentry)
{
    $row = array();
    $row['details'] = array(
        substr($logentry['package'], 0, strrpos($logentry['package'], '.')),
        $logentry['action'],
        $logentry['date'] . ', ' . $logentry['time']
    );
    $rows[] = $row;
    // Pagination looks bad on very small width tables...let's cut out at 20 and not paginate at all
    if ($layout['total_columns'] >= 3 && count($rows) > 20)
        break;
}

///////////////////////////////////////////////////////////////////////////////
// Table
///////////////////////////////////////////////////////////////////////////////

$options = array(
    'id' => 'activity_list',
    'default_rows' => 10,
    'no_action' => TRUE
);
if ($layout['total_columns'] == 1) {
    $options['sort-default-col'] = 2;
    $options['sort-default-dir'] = 'desc';
} else if ($layout['total_columns'] == 2) {
    $options['responsive'] = array(2 => 'none');
    $options['sort'] = FALSE;
    $options['default_rows'] = 20;
} else if ($layout['total_columns'] >= 3) {
    $options['responsive'] = array(1 => 'none', 2 => 'none');
    $options['paginate'] = FALSE;
    $options['sort'] = FALSE;
    $options['default_rows'] = 20;
}

echo summary_table(
     lang('software_updates_recent_software_activity'),
     $buttons,
     $headers,
     $rows,
     $options
);
