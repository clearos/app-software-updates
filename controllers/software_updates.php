<?php

/**
 * Software updates controller.
 *
 * @category   apps
 * @package    software-updates
 * @subpackage controllers
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2012 ClearFoundation
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
// C L A S S
///////////////////////////////////////////////////////////////////////////////

/**
 * Software updates controller.
 *
 * @category   apps
 * @package    software-updates
 * @subpackage controllers
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2012 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/software_updates/
 */

class Software_Updates extends ClearOS_Controller
{
    /**
     * Software_Updates summary view.
     *
     * @return view
     */

    function index()
    {
        // Load libraries
        //---------------

        $this->lang->load('software_updates');
        $this->load->library('base/Yum');

        // Load views
        //-----------

        // If yum is running, show progress
        if ($this->yum->is_yum_busy()) {
            redirect('software_updates/updates/busy');
            return;
        } else if ($this->yum->is_busy()) {
            redirect('software_updates/updates/progress');
            return;
        }

        $views = array('software_updates/settings', 'software_updates/updates', 'software_updates/activity');

        $this->page->view_forms($views, lang('software_updates_app_name'));
    }
}
