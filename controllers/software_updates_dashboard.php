<?php

/**
 * Software Updates Dashboard controller.
 *
 * @category   apps
 * @package    software_updates
 * @subpackage controllers
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2015 ClearFoundation
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

class Software_Updates_Dashboard extends ClearOS_Controller
{
    /**
     * Dashboard default controller.
     *
     * @return view
     */

    function index()
    {
        // Load libraries
        //---------------

		$this->lang->load('software_updates');

        $this->page->view_form('software_updates/dashboard/available', NULL, lang('software_updates_available_updates'));
	}

    /**
     * Recent activity controller
     *
     * @param string layout
     *
     * @return view
     */

    function recent_activity($layout)
    {
        // Load dependencies
        //------------------

        $this->lang->load('software_updates');
        $this->load->library('base/Stats');

        // Load view data
        //---------------

        $data = array();
        // Layout comes from Dashboard widget in format n-n-n
        // Last number is number of columns which gives us an idea of how big our table is
        list($data['layout']['row'], $data['layout']['col'], $data['layout']['total_columns']) = explode('-', $layout);
        try {
            $data['log'] = array_reverse($this->stats->get_yum_log(10000));
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }

        // Load views
        //-----------

        $this->page->view_form('software_updates/dashboard/activity', $data, lang('software_updates_recent_software_activity'));
    }
}
