<?php

/**
 * Software updates class.
 *
 * @category   apps
 * @package    software-updates
 * @subpackage libraries
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2012-2014 ClearFoundation
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

use \clearos\apps\base\Configuration_File as Configuration_File;
use \clearos\apps\base\Engine as Engine;
use \clearos\apps\base\File as File;
use \clearos\apps\base\Shell as Shell;
use \clearos\apps\base\Software as Software;
use \clearos\apps\base\Yum as Yum;
use \clearos\apps\tasks\Cron as Cron;

clearos_load_library('base/Configuration_File');
clearos_load_library('base/Engine');
clearos_load_library('base/File');
clearos_load_library('base/Shell');
clearos_load_library('base/Software');
clearos_load_library('base/Yum');
clearos_load_library('tasks/Cron');

// Exceptions
//-----------

use \Exception as Exception;
use \clearos\apps\base\File_Not_Found_Exception as File_Not_Found_Exception;
use \clearos\apps\base\Yum_Busy_Exception as Yum_Busy_Exception;

clearos_load_library('base/File_Not_Found_Exception');
clearos_load_library('base/Yum_Busy_Exception');

///////////////////////////////////////////////////////////////////////////////
// C L A S S
///////////////////////////////////////////////////////////////////////////////

/**
 * Software updates class.
 *
 * @category   apps
 * @package    software-updates
 * @subpackage libraries
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2012-2014 ClearFoundation
 * @license    http://www.gnu.org/copyleft/lgpl.html GNU Lesser General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/software_updates/
 */

class Software_Updates extends Engine
{
    ///////////////////////////////////////////////////////////////////////////////
    // C O N S T A N T S
    ///////////////////////////////////////////////////////////////////////////////

    const COMMAND_YUM = '/usr/bin/yum';
    const COMMAND_UPDATE = '/usr/sbin/software-updates';
    const FILE_CONFIG = '/etc/clearos/software_updates.conf';
    const FILE_CRON_CONFIGLET = 'app-software-updates';

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
     * @param string $type type
     *
     * @return void
     * @throws Engine_Exception, Yum_Busy_Exception
     */

    public function get_available_updates($type = 'all')
    {
        clearos_profile(__METHOD__, __LINE__);

        // Wait around if yum is busy
        //---------------------------

        $yum = new Yum();

        $counter = 0;

        while ($yum->is_busy()) {
            if ($counter > 100)
                throw new Yum_Busy_Exception();

            sleep(3);
            $counter++;
        }

        // Grab updates
        //-------------

        $options['validate_exit_code'] = FALSE;

        $shell = new Shell();
        $shell->execute(self::COMMAND_YUM, "check-update", TRUE, $options);
        $raw_output = $shell->get_output();

        $list = array();
        $header_done = FALSE;

        foreach ($raw_output as $line) {
            if ($header_done) {
                $raw_items = preg_split('/\s+/', $line);

                if (($type === 'first_boot') 
                    && !(preg_match('/^app-/', $raw_items[0]) 
                    || preg_match('/^clearos-release-/', $raw_items[0]) 
                    || preg_match('/^clearos-logos-/', $raw_items[0]) 
                    || preg_match('/^theme-default-/', $raw_items[0]))
                )
                    continue;

                // Skip invalid lines (obsoleting packages)
                if (preg_match('/^\s/', $line))
                    continue;

                if (count($raw_items) !== 3)
                    continue;

                $item['package'] = preg_replace('/\.[\w]+$/', '', $raw_items[0]);
                $item['arch'] = preg_replace('/.*\./', '', $raw_items[0]);
                $item['version'] = preg_replace('/.*:/', '', $raw_items[1]);
                $item['full_version'] = $raw_items[1];
                $item['repo'] = $raw_items[2];

                try {
                    $software = new Software($item['package']);

                    if ($software->is_installed())
                        $item['summary'] = $software->get_summary();
                    else
                        $item['summary'] = $item['package'];
                } catch (Exception $e) {
                    // Not fatal
                }

                $list[] = $item;
            } else if (preg_match('/^\s*$/', $line) || preg_match('/^Obsoleting\s+/', $line)) {
                $header_done = TRUE;
            }
        }

        return $list;
    }

    /**
     * Returns state of automatic updates.
     *
     * @return boolean state of automatic updates.
     * @throws Engine_Exception
     */

    public function get_automatic_updates_state()
    {
        clearos_profile(__METHOD__, __LINE__);

        $config = array();

        try {
            $file = new Configuration_File(self::FILE_CONFIG);
            $config = $file->load();
        } catch (File_Not_Found_Exception $e) {
            // Not fatal
        }

        if (! isset($config['automatic']))
            $automatic = FALSE;
        else 
            $automatic = (preg_match('/enabled/i', $config['automatic'])) ? TRUE : FALSE;

        return $automatic;
    }

    /**
     * Runs update.
     *
     * @param string $type type of update (all or first_boot)
     *
     * @return void
     */

    public function run_update($type = 'all')
    {
        clearos_profile(__METHOD__, __LINE__);

        $list = array();

        $raw_list = $this->get_available_updates($type);

        foreach ($raw_list as $details)
            $list[] = $details['package'];

        if (count($list) === 0) {
            clearos_log('software-updates', 'no updates required');
            return;
        }

        foreach ($list as $package)
            clearos_log('software-updates', 'requesting package: ' . $package);

        try {
            $yum = new Yum();
            if ($type === 'all')
                $yum->run_upgrade($list);
            else
                $yum->install($list);
        } catch (Exception $e) {
            // Not fatal
        }
    }

    /**
     * Sets state of automatic updates.
     *
     * @param boolean $state state
     *
     * @return void
     * @throws Engine_Exception
     */

    public function set_automatic_updates_state($state)
    {
        clearos_profile(__METHOD__, __LINE__);

        $file = new File(self::FILE_CONFIG);

        if ($file->exists())
            $file->delete();

        $state_value = ($state) ? 'enabled' : 'disabled';

        $file->create('root', 'root', '0644');
        $file->add_lines("automatic = $state_value\n");
    }
    /**
     * Sets auto-update taks time.
     *
     * Auto-update will set a cron job roughly 24 hours from the time this
     * method is called.  The time is randomized a bit to spread the load
     * on the download servers.
     *
     * @return void
     * @throws Engine_Exception
     */

    public function set_automatic_updates_time()
    {
        clearos_profile(__METHOD__, __LINE__);

        $cron = new Cron();

        if ($cron->exists_configlet(self::FILE_CRON_CONFIGLET))
            $cron->delete_configlet(self::FILE_CRON_CONFIGLET);

        $nextday = date('w') + 1;

        $cron->add_configlet_by_parts(
            self::FILE_CRON_CONFIGLET,
            rand(0, 59), rand(1, 6), '*', '*', $nextday,
            'root',
            self::COMMAND_UPDATE . ' >/dev/null 2>&1'
        );

        // Clean out cruft files created by rpm upgrades
        $cruftfiles = array('rpmsave', 'rpmnew', 'rpmorig');

        foreach ($cruftfiles as $cruft) {
            $file = new File(self::FILE_CRON_CONFIGLET . '.' . $cruft);

            if ($file->exists())
                $file->delete();
        }
    }
}
