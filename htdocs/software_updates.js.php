<?php

/**
 * Javascript helper for Software_Updates.
 *
 * @category   apps
 * @package    software-updates
 * @subpackage javascript
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2003-2011 ClearFoundation
 * @license    http://www.gnu.org/copyleft/lgpl.html GNU Lesser General Public License version 3 or later
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
// B O O T S T R A P
///////////////////////////////////////////////////////////////////////////////

$bootstrap = getenv('CLEAROS_BOOTSTRAP') ? getenv('CLEAROS_BOOTSTRAP') : '/usr/clearos/framework/shared';
require_once $bootstrap . '/bootstrap.php';

///////////////////////////////////////////////////////////////////////////////
// T R A N S L A T I O N S
///////////////////////////////////////////////////////////////////////////////

clearos_load_language('software_updates');

///////////////////////////////////////////////////////////////////////////////
// J A V A S C R I P T
///////////////////////////////////////////////////////////////////////////////

header('Content-Type: application/x-javascript');
?>

$(document).ready(function() {

    // Translations
    //-------------

    lang_loading = '<?php echo lang("software_updates_loading_updates_message"); ?>';
    lang_no_updates_required = '<?php echo lang("software_updates_system_is_up_to_date"); ?>';
    lang_wizard_updates = '<?php echo lang("software_updates_wizard_latest_necessary"); ?>';

    $('#update_all').attr('disabled', 'disabled');

    // Wizard previous/next button handling
    //-------------------------------------

    $('#wizard_nav_next').on('click', function(e) {
        if ($('#software_updates_complete').html() == 'done') {
            // Allow to go to next step
        } else {
            e.preventDefault();
            clearos_modal_infobox_open('wizard_next_showstopper');
        }
    });

    // Main
    //-----

    if ($('#updates_list').length != 0) {
        if ($(location).attr('href').match('.*\/first_boot') != null)
            get_list('first_boot');
        else
            get_list('all');
    }

    if ($('#overall').length != 0) {
        $('#wizard_nav_previous').hide();
        $('#wizard_nav_next').hide();
        get_progress();
    }
});

function get_list(type) {
    var table_updates_list = get_table_updates_list();
    table_updates_list.fnClearTable();

    $.ajax({
        type: 'GET',
        dataType: 'json',
        url: '/app/software_updates/updates/get_available_updates/' + type,
        data: '',
        success: function(json) {
            show_list(json);
        },
        error: function(xhr, text, err) {
        }
    });
}

function get_progress() {
    $.ajax({
        url: '/app/software_updates/updates/get_progress',
        method: 'GET',
        dataType: 'json',
        success : function(json) {
            show_progress(json);
        },
        error: function(xhr, text, err) {
        }
    });
}

function show_list(json) {
    var table_updates_list = get_table_updates_list();
    table_updates_list.fnClearTable();

    // On the first boot wizard, only "app-" packages are included.
    // We can use the more human-readable "summary" field instead
    // of the package name.

    if (!json.list || (json.list.length == 0)) {
        $('#updates_list_container').hide();
        $('#software_updates_complete_container').show();
        $('#software_updates_complete').html('done');
        $('#wizard_next_showstopper').remove();
        // hack to remove modal backdrop
        $('.modal-backdrop').remove();
        return;
    }
 
    $('#update_all').removeAttr('disabled');

    $('#wizard_next_showstopper-message').html(lang_wizard_updates);
    for (var index = 0 ; index < json.list.length; index++) {
        if ($(location).attr('href').match('.*\/first_boot') != null) {
            table_updates_list.fnAddData([
                json.list[index].summary,
                json.list[index].version
            ]);
        } else {
            table_updates_list.fnAddData([
                json.list[index].package,
                json.list[index].version,
                json.list[index].arch,
                json.list[index].repo
            ]);
        }
    }

    table_updates_list.fnAdjustColumnSizing();
}

function show_progress(json) {

    // If no wc-yum process is running, some other user or service is running
    // yum which we can't latch on to output.  Show the busy widget.
    if (json.busy && !json.wc_busy) {
        $('#yum_busy').show();
        $('#yum_progress').hide();
    } else {
        $('#yum_busy').hide();
        $('#yum_progress').show();
    }
        
    clearos_set_progress_bar('progress', parseInt(json.progress), null);
    clearos_set_progress_bar('overall', parseInt(json.overall), null);

    if (json.code === 0) {
        $('#details').html(json.details);
    } else if (json.code === -999) {
        // Do nothing...no data yet
    } else {
        // Uh oh...something bad happened
        clearos_set_progress_bar('progress', 0, null);
        clearos_set_progress_bar('overall', 0, null);
        $('#details').html(json.errmsg);

        if ($('#wizard_nav_previous').length != 0) {
            $('#wizard_nav_previous').show();
            $('#wizard_nav_next').show();
        } else {
            $('#yum_complete').show();
        }

        return;
    }

    if (json.overall == 100) {
        if ($('#wizard_nav_previous').length != 0) {
            $('#wizard_nav_previous').show();
            $('#wizard_nav_next').show();
        }

        $('#yum_complete').show();
        $('#software_updates_complete').html('done');
        window.setTimeout(get_progress, 5000);
    } else {
        window.setTimeout(get_progress, 2000);
    }
}

// vim: syntax=javascript ts=4
