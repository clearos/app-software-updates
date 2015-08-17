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

$update_url = ($first_boot) ? 'first_boot' : 'all' ;

$buttons = array(
    anchor_custom("/app/software_updates/updates/run_update/$update_url", lang('software_updates_update_all'), 'high', array('id' => 'update_all'))
);

///////////////////////////////////////////////////////////////////////////////
// Headers
///////////////////////////////////////////////////////////////////////////////

if ($first_boot) {
    $headers = array(
        lang('software_updates_package'),
        lang('base_version')
    );
} else {
    $headers = array(
        lang('software_updates_package'),
        lang('base_version'),
        lang('software_updates_type'),
        lang('software_updates_repository')
    );
}

///////////////////////////////////////////////////////////////////////////////
// Items
///////////////////////////////////////////////////////////////////////////////

// Done in Ajax

///////////////////////////////////////////////////////////////////////////////
// List table
///////////////////////////////////////////////////////////////////////////////

echo "<div id='updates_list_container'>";

$options = array(
    'id' => 'updates_list',
    'no_action' => TRUE,
    'empty_table_message' => loading('normal', lang('software_updates_loading_updates_message')),
);

if ($first_boot) {
    $options['paginate'] = FALSE;
} else {
    $options['paginate'] = TRUE;
    $options['paginate_large'] = TRUE;
    $options['default_rows'] = 20;
    $options['responsive'] = array(3 => 'none');
}

echo summary_table(
    lang('software_updates_available_updates'),
    $buttons,
    $headers,
    NULL,
    $options
);
echo "</div>";

echo infobox_info(
    lang('software_updates_software_up_to_date'), 
    lang('software_updates_the_latest_software_updates_are_installed'),
    array('id' => 'software_updates_complete_container', 'hidden' => TRUE)
);
echo "<div id='software_updates_complete' style='display:none;'></div>";
if ($first_boot)
    echo modal_info("wizard_next_showstopper", lang('base_error'), lang('software_updates_loading_updates_message'), array('type' => 'warning'));
