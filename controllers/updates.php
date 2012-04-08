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

        $this->load->library('pptpd/PPTPd');
        $this->lang->load('pptpd');

        // Set validation rules
        //---------------------
         
        $this->form_validation->set_policy('remote_ip', 'pptpd/PPTPd', 'validate_ip_range');
        $this->form_validation->set_policy('local_ip', 'pptpd/PPTPd', 'validate_ip_range');
        $this->form_validation->set_policy('wins', 'pptpd/PPTPd', 'validate_wins_server');
        $this->form_validation->set_policy('dns', 'pptpd/PPTPd', 'validate_dns_server');
        $form_ok = $this->form_validation->run();

        // Handle form submit
        //-------------------

        if (($this->input->post('submit') && $form_ok)) {
            try {
                $this->pptpd->set_remote_ip($this->input->post('remote_ip'));
                $this->pptpd->set_local_ip($this->input->post('local_ip'));
                $this->pptpd->set_wins_server($this->input->post('wins'));
                $this->pptpd->set_dns_server($this->input->post('dns'));
                $this->pptpd->reset(TRUE);

                $this->page->set_status_updated();
            } catch (Exception $e) {
                $this->page->view_exception($e);
                return;
            }
        }

        // Load view data
        //---------------

        try {
            $data['form_type'] = $form_type;
            $data['local_ip'] = $this->pptpd->get_local_ip();
            $data['remote_ip'] = $this->pptpd->get_remote_ip();
            $data['wins'] = $this->pptpd->get_wins_server();
            $data['dns'] = $this->pptpd->get_dns_server();
            $data['auto_configure'] = $this->pptpd->get_auto_configure_state();
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }

        // Load views
        //-----------

        $this->page->view_form('software_updates/updates', $data, lang('software_updates_available_updates'));
    }

    /**
     * Returns list of updates.
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

        // Grab data
        //----------

        header('Cache-Control: no-cache, must-revalidate');
        header('Content-type: application/json');

        try {
            if ($type === 'app')
                $data['list'] = $this->software_updates->get_available_app_updates();
            else
                $data['list'] = $this->software_updates->get_available_updates();

            $data['code'] = 0;
            echo json_encode($data);
        } catch (Yum_Busy_Exception $e) {
            echo json_encode(array('code' => clearos_exception_code($e), 'errmsg' => lang('software_updates_updates_system_busy')));
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
     * Install all updates.
     *
     * @return view
     */

    function update_all()
    {
        // Load dependencies
        //------------------

        $this->load->library('software_updates/Software_Updates');

        $this->software_updates->run_update_all();
    }
}
