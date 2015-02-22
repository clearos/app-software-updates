<?php

/////////////////////////////////////////////////////////////////////////////
// General information
/////////////////////////////////////////////////////////////////////////////

$app['basename'] = 'software_updates';
$app['version'] = '1.6.8';
$app['release'] = '1';
$app['vendor'] = 'ClearFoundation';
$app['packager'] = 'ClearFoundation';
$app['license'] = 'GPLv3';
$app['license_core'] = 'LGPLv3';
$app['description'] = lang('software_updates_app_description');

/////////////////////////////////////////////////////////////////////////////
// App name and categories
/////////////////////////////////////////////////////////////////////////////

$app['name'] = lang('software_updates_app_name');
$app['category'] = lang('base_category_system');
$app['subcategory'] = lang('base_subcategory_operating_system');

/////////////////////////////////////////////////////////////////////////////
// Controllers
/////////////////////////////////////////////////////////////////////////////

$app['controllers']['software_updates']['title'] = $app['name'];
$app['controllers']['settings']['title'] = lang('base_settings');
$app['controllers']['updates']['title'] = lang('software_updates_updates');
$app['controllers']['first_boot']['title'] = lang('software_updates_updates');

// Wizard extras
$app['controllers']['software_updates']['wizard_name'] = lang('software_updates_app_name');
$app['controllers']['software_updates']['wizard_description'] = lang('software_updates_wizard_description');
$app['controllers']['software_updates']['inline_help'] = array(
    lang('software_updates_please_be_patient') => lang('software_updates_please_be_patient_detail'),
);

/////////////////////////////////////////////////////////////////////////////
// Packaging
/////////////////////////////////////////////////////////////////////////////

$app['requires'] = array(
    'app-network',
);

$app['core_requires'] = array(
    'app-events-core',
    'app-network-core',
    'app-tasks-core',
);

$app['core_directory_manifest'] = array(
    '/var/clearos/software_updates' => array(),
    '/var/clearos/events/software_updates' => array(),
);

$app['core_file_manifest'] = array(
    'filewatch-software-updates-event.conf' => array('target' => '/etc/clearsync.d/filewatch-software-updates-event.conf'),
    'software_updates.conf' => array(
        'target' => '/etc/clearos/software_updates.conf',
        'config' => TRUE,
        'config_params' => 'noreplace',
    ),
    'app-software-updates.cron' => array(
        'target' => '/etc/cron.d/app-software-updates',
        'config' => TRUE,
        'config_params' => 'noreplace',
    ),
    'software-updates' => array(
        'target' => '/usr/sbin/software-updates',
        'mode' => '0755',
    ),
);
