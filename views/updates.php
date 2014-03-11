<?php

/**
 * Software updates overview.
 *
 * @category   apps
 * @package    software-updates
 * @subpackage views
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2011 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link       http://www.clearcenter.com/support/documentation/clearos/software_updates/
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
// Buttons
///////////////////////////////////////////////////////////////////////////////

if ($first_boot) {
    $buttons = array();
} else {
    $buttons = array(
        anchor_custom('/app/software_updates/updates/run_update/all', lang('software_updates_update_all'))
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

echo "<div id='updates_list_container'>";

$options['id'] = 'updates_list';
$options['no_action'] = TRUE;
$options['empty_table_message'] = "<div class='theme-loading-small' style='margin: 5px 5px;'>" . lang('software_updates_loading_updates_message') . "</div>";

echo summary_table(
    lang('software_updates_available_updates'),
    $buttons,
    $headers,
    NULL,
    $options
);
echo "</div>";

echo "<div id='software_updates_complete_container' style='display:none;'>";
echo infobox_highlight(
    lang('software_updates_software_up_to_date'), 
    lang('software_updates_the_latest_software_updates_are_installed')
);
echo "</div>";
echo "<div id='software_updates_complete' style='display:none;'></div>";
