<?php

/**
 * Software repository overview.
 *
 * @category   Apps
 * @package    Software_Repository
 * @subpackage Views
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2011 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link       http://www.clearcenter.com/support/documentation/clearos/software_repository/
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
$this->lang->load('software_repository');

///////////////////////////////////////////////////////////////////////////////
// Infoboxes
///////////////////////////////////////////////////////////////////////////////

if ($updates_complete) {
    echo "<div id='software_updates_complete'></div>";
    echo infobox_highlight(
        lang('software_updates_updates_complete'), 
        lang('software_updates_updates_complete_detail')
    );
    return;
}

echo "<div id='software_repository_warning_box' style='display: none'>";
echo infobox_warning(lang('base_warning'), "<div id='software_repository_warning'></div>");
echo "</div>";

///////////////////////////////////////////////////////////////////////////////
// Buttons
///////////////////////////////////////////////////////////////////////////////

if ($first_boot) {
    $buttons = array();
} else {
    $buttons = array(
        anchor_custom('/app/software_updates/updates/update_all', lang('software_updates_update_all'))
    );
}

///////////////////////////////////////////////////////////////////////////////
// Headers
///////////////////////////////////////////////////////////////////////////////

$headers = array(
    lang('software_updates_package'),
    lang('base_version'),
    lang('software_updates_type'),
    lang('software_updates_repository'),
);

///////////////////////////////////////////////////////////////////////////////
// Items
///////////////////////////////////////////////////////////////////////////////

// Done in Ajax

///////////////////////////////////////////////////////////////////////////////
// List table
///////////////////////////////////////////////////////////////////////////////

$options['id'] = 'updates_list';
$options['no_action'] = TRUE;

echo summary_table(
    lang('software_updates_available_updates'),
    $buttons,
    $headers,
    NULL,
    $options
);
