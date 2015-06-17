<?php

/**
 * Software updates overview.
 *
 * @category   apps
 * @package    software-updates
 * @subpackage views
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2015 ClearFoundation
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
// Headers
///////////////////////////////////////////////////////////////////////////////

$headers = array(
    lang('software_updates_package'),
    lang('base_version')
);

///////////////////////////////////////////////////////////////////////////////
// Anchor
///////////////////////////////////////////////////////////////////////////////

$buttons = array(
    anchor_custom("/app/software_updates", lang('base_view'), 'high')
);

///////////////////////////////////////////////////////////////////////////////
// Items
///////////////////////////////////////////////////////////////////////////////

// Done in Ajax

///////////////////////////////////////////////////////////////////////////////
// List table
///////////////////////////////////////////////////////////////////////////////

$options = array(
    'id' => 'updates_list',
    'no_action' => TRUE,
    'empty_table_message' => loading('normal', lang('software_updates_loading_updates_message')),
    'paginate' => TRUE,
    'paginate_large' => TRUE,
    'default_rows' => 5,
);

echo summary_table(
    lang('software_updates_available_updates'),
    $buttons,
    $headers,
    NULL,
    $options
);

// Script below used to fetch list
echo "<script type='text/javascript'>
        $(document).ready(function() {

            var table_updates_list = get_table_updates_list();
            table_updates_list.fnClearTable();

            $.ajax({
                type: 'GET',
                dataType: 'json',
                url: '/app/software_updates/updates/get_available_updates/all',
                data: '',
                success: function(json) {
                    for (var index = 0 ; index < json.list.length; index++) {
                        table_updates_list.fnAddData([
                            json.list[index].package,
                            json.list[index].version,
                        ]);
                    }

                    table_updates_list.fnAdjustColumnSizing();
                },
                error: function(xhr, text, err) {
                }
            });
        });
      </script>
";
