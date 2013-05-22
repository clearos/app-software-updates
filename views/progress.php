<?php

/**
 * Software install progress view.
 *
 * @category   apps
 * @package    software-updates
 * @subpackage views
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2012 ClearFoundation
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
// Busy Infobox
///////////////////////////////////////////////////////////////////////////////

echo "<div id='yum_busy' style='display:none;'>";
echo infobox_warning(
    lang('software_updates_progress'), 
    "<div class='theme-loading-small'></div>" .lang('software_updates_busy_message')
);
echo "</div>";

///////////////////////////////////////////////////////////////////////////////
// Form
///////////////////////////////////////////////////////////////////////////////

echo "<div id='yum_progress' style='display:none;'>";

echo "
<br>
<div id='summary-info'>
    <h3>" . lang('software_updates_overall_progress') . "</h3>" . 
    progress_bar('overall', array('input' => 'overall')) . "

    <h3>" . lang('software_updates_current_progress') . "</h3>" .
    progress_bar('progress', array('input' => 'progress')) . "

    <h3>" . lang('software_updates_details') . "</h3>
    <div id='details'></div>
</div>
";

echo "</div>";

echo "<br><br>";
echo "<div id='yum_complete' style='display:none;'>";
echo "<p align='center'>" . anchor_custom('/app/software_updates', lang('software_updates_return_to_overview')) . "</p>";
echo "</div>";
