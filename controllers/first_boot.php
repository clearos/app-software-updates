<?php

/**
 * Software updates controller.
 *
 * @category   Apps
 * @package    Software_Updates
 * @subpackage Controllers
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
// D E P E N D E N C I E S
///////////////////////////////////////////////////////////////////////////////

use \Exception as Exception;
use \clearos\apps\base\Yum_Busy_Exception as Yum_Busy_Exception;

///////////////////////////////////////////////////////////////////////////////
// C L A S S
///////////////////////////////////////////////////////////////////////////////

/**
 * Software updates controller.
 *
 * @category   Apps
 * @package    Software_Updates
 * @subpackage Controllers
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2012 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/software_updates/
 */

class First_Boot extends ClearOS_Controller
{
    /**
     * First boot updates controller.
     *
     * @return view
     */

    function index($os_name = '')
    {
        // Load dependencies
        //------------------

        $this->lang->load('software_updates');
        $this->load->library('software_updates/Software_Updates');

        // Load view data
        //---------------

        try {
            if (empty($os_name) || ($os_name === 'community'))
                $data['updates_complete'] = $this->software_updates->get_first_boot_updates_complete_state();
            else
                $data['updates_complete'] = FALSE;
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }

        // Load views
        //-----------

        $data['os_name'] = $os_name; 
        $data['first_boot'] = TRUE;

        $this->page->view_form('software_updates/updates', $data, lang('software_updates_available_updates'));
    }

    /**
     * Install all updates.
     *
     * @return view
     */

    function update()
    {
        // Load dependencies
        //------------------

        $this->lang->load('software_updates');
        $this->load->library('software_updates/Software_Updates');

        // Start update
        //-------------

        // FIXME
        // $this->software_updates->.....();

        // Load views
        //-----------

        $this->page->view_form('software_updates/progress', NULL, lang('software_updates_install_progress'));
    }
}
