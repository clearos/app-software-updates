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
 * @category   apps
 * @package    software-updates
 * @subpackage controllers
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2012 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/software_updates/
 */

class Updates extends ClearOS_Controller
{
    /**
     * Updates controller.
     *
     * @return view
     */

    function index()
    {
        // Load dependencies
        //------------------

        $this->lang->load('software_updates');

        // Load views
        //-----------

        $this->page->view_form('software_updates/updates', $data, lang('software_updates_available_updates'));
    }

    /**
     * Show busy view.
     *
     * @return view
     */

    function busy()
    {
        // Load dependencies
        //------------------

        $this->lang->load('software_updates');

        $this->page->view_form('software_updates/busy', NULL, lang('software_updates_install_progress'));
    }

    /**
     * Returns list of updates.
     *
     * @param string $type    type
     *
     * @return JSON
     */

    function get_available_updates($type)
    {
        clearos_profile(__METHOD__, __LINE__);

        // Load dependencies
        //------------------

        $this->lang->load('software_updates');
        $this->load->library('software_updates/Software_Updates');
        $this->load->library('base/OS');

        // Grab data
        //----------

        header('Cache-Control: no-cache, must-revalidate');
        header('Content-type: application/json');

        try {
            $data['list'] = $this->software_updates->get_available_updates($type);
            $data['code'] = 0;

            echo json_encode($data);
        } catch (Yum_Busy_Exception $e) {
            echo json_encode(array('code' => clearos_exception_code($e), 'errmsg' => lang('software_updates_software_updates_system_busy')));
        } catch (Exception $e) {
            echo json_encode(array('code' => clearos_exception_code($e), 'errmsg' => clearos_exception_message($e)));
        }
    }

    /**
     * Ajax progress controller
     *
     * @return JSON
     */

    function get_progress()
    {
        clearos_profile(__METHOD__, __LINE__);

        // Load dependencies
        //------------------

        $this->load->library('base/Yum');

        // Grab data
        //----------

        header('Cache-Control: no-cache, must-revalidate');
        header('Content-type: application/json');

        // TODO: this code (yum->get_status) is now in the Yum class. To merge.
        try {
            $logs = $this->yum->get_logs();
            $logs = array_reverse($logs);

            foreach ($logs as $log) {
                $last = json_decode($log);

                // Make sure we're getting valid JSON
                if (!is_object($last))
                    continue;

                echo json_encode(
                    array(
                        'code' => $last->code,
                        'details' => $last->details,
                        'progress' => $last->progress,
                        'overall' => $last->overall,
                        'errmsg' => $last->errmsg,
                        'busy' => $this->yum->is_busy(),
                        'wc_busy' => $this->yum->is_wc_busy()
                    )
                );
                return;
            }

            echo json_encode(array('code' => -999, 'errmsg' => lang('software_updates_no_data_available')));
        } catch (Exception $e) {
            echo json_encode(array('code' => clearos_exception_code($e), 'errmsg' => clearos_exception_message($e)));
        }
    }

    /**
     * Show progress view.
     *
     * @return view
     */

    function progress()
    {
        // Load dependencies
        //------------------

        $this->lang->load('software_updates');

        $this->page->view_form('software_updates/progress', NULL, lang('software_updates_install_progress'));
    }

    /**
     * Install all updates.
     *
     * @param string $type    type
     *
     * @return view
     */

    function run_update($type = 'all')
    {
        $this->load->library('software_updates/Software_Updates');

        $this->software_updates->run_update($type);

        // Redirect to avoid page refresh issues
        if ($type === 'all')
            redirect('/software_updates/updates/busy');
        else
            redirect('/software_updates/updates/progress');
    }
}
