<?php

/**
 * Software updates class.
 *
 * @category   Apps
 * @package    Software_Updates
 * @subpackage Libraries
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2012 ClearFoundation
 * @license    http://www.gnu.org/copyleft/lgpl.html GNU Lesser General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/software_updates/
 */

///////////////////////////////////////////////////////////////////////////////
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU Lesser General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Lesser General Public License for more details.
//
// You should have received a copy of the GNU Lesser General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
///////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////
// N A M E S P A C E
///////////////////////////////////////////////////////////////////////////////

namespace clearos\apps\software_updates;

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
// D E P E N D E N C I E S
///////////////////////////////////////////////////////////////////////////////

// Classes
//--------

use \clearos\apps\base\Engine as Engine;
use \clearos\apps\base\Shell as Shell;
use \clearos\apps\base\Yum as Yum;

clearos_load_library('base/Engine');
clearos_load_library('base/Shell');
clearos_load_library('base/Yum');

// Exceptions
//-----------

use \clearos\apps\base\Yum_Busy_Exception as Yum_Busy_Exception;

clearos_load_library('base/Yum_Busy_Exception');

///////////////////////////////////////////////////////////////////////////////
// C L A S S
///////////////////////////////////////////////////////////////////////////////

/**
 * Software updates class.
 *
 * @category   Apps
 * @package    Software_Updates
 * @subpackage Libraries
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2012 ClearFoundation
 * @license    http://www.gnu.org/copyleft/lgpl.html GNU Lesser General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/software_updates/
 */

class Software_Updates extends Engine
{
    ///////////////////////////////////////////////////////////////////////////////
    // C O N S T A N T S
    ///////////////////////////////////////////////////////////////////////////////

    const COMMAND_WC_YUM = '/usr/sbin/wc-yum';
    const COMMAND_YUM = '/usr/bin/yum';

    ///////////////////////////////////////////////////////////////////////////////
    // M E T H O D S
    ///////////////////////////////////////////////////////////////////////////////

    /**
     * Software updates constructor.
     */

    public function __construct()
    {
        clearos_profile(__METHOD__, __LINE__);
    }

    /**
     * Returns list of available updates.
     *
     * @return void
     * @throws Engine_Exception, Yum_Busy_Exception
     */

    public function get_available_updates()
    {
        clearos_profile(__METHOD__, __LINE__);

        return $this->_get_updates('all');
    }

    /**
     * Returns list of available app updates.
     *
     * @return void
     * @throws Engine_Exception, Yum_Busy_Exception
     */

    public function get_available_app_updates()
    {
        clearos_profile(__METHOD__, __LINE__);

        return $this->_get_updates('app');
    }

    /**
     * Runs update all.
     *
     * @return void
     */

    public function run_update_all()
    {
        clearos_profile(__METHOD__, __LINE__);

//        $list = $this->get_available_updates();
        $list = $this->get_available_app_updates();

        print_r($list);

 //       $yum = new Yum();
    }

    ///////////////////////////////////////////////////////////////////////////////
    // P R I V A T E  M E T H O D S
    ///////////////////////////////////////////////////////////////////////////////

    /**
     * Returns update list.
     *
     * @return void
     */

    public function _get_updates($filter)
    {
        clearos_profile(__METHOD__, __LINE__);

        // Wait around if yum is busy
        //---------------------------

        $yum = new Yum();

        $counter = 0;

        while ($yum->is_busy()) {
            if ($counter > 5)
                throw new Yum_Busy_Exception();

            sleep(3);
            $counter++;
        }

        // Grab updates
        //-------------

        $shell = new Shell();

        $options['validate_exit_code'] = FALSE;

        $shell->execute(self::COMMAND_YUM, 'check-update', TRUE, $options);

        $raw_output = $shell->get_output();
        $list = array();
        $header_done = FALSE;

        foreach ($raw_output as $line) {
            if ($header_done)  {
                $raw_items = preg_split('/\s+/', $line);

                if (($filter === 'app') && !preg_match('/^app-/', $raw_items[0]))
                    continue;

                $item['package'] = preg_replace('/\..*/', '', $raw_items[0]);
                $item['arch'] = preg_replace('/.*\./', '', $raw_items[0]);
                $item['version'] = preg_replace('/.*:/', '', $raw_items[1]);
                $item['full_version'] = $raw_items[1];
                $item['repo'] = $raw_items[2];

                $list[] = $item;
            } else if (preg_match('/^\s*$/', $line)) {
                $header_done = TRUE;
            }
        }

        return $list;
    }
}
