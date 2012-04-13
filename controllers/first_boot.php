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
     * @param string $os_name requested OS
     *
     * @return view
     */

    function index($os_name = '')
    {
        // Load dependencies
        //------------------

        $this->lang->load('software_updates');
        $this->load->library('software_updates/Software_Updates');
        $this->load->library('base/OS');

        // Load views
        //-----------

        // The os_name is empty when coming from a "back button" on the
        // next wizard page.  Throw it back to "Select Edition" on
        $installed_os = $this->os->get_name();

        if (empty($os_name)) {
            if (preg_match('/ClearOS Commnity/', $installed_os))
                $os_name = 'community';
            else
                $os_name = 'professional';

            redirect('/software_updates/first_boot/index/' . $os_name);
        }

        $data['os_name'] = $os_name; 
        $data['first_boot'] = TRUE;

        $this->page->view_form('software_updates/updates', $data, lang('software_updates_available_updates'));
    }
}
