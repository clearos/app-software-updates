<?php

/////////////////////////////////////////////////////////////////////////////
// General information
/////////////////////////////////////////////////////////////////////////////

$app['basename'] = 'software_updates';
$app['version'] = '1.0.16';
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
$app['controllers']['first_boot']['wizard_name'] = lang('software_updates_app_name');
$app['controllers']['first_boot']['wizard_description'] = lang('software_updates_wizard_description');
$app['controllers']['first_boot']['inline_help'] = array(
    lang('software_updates_please_be_patient') => lang('software_updates_please_be_patient_detail'),
);

$app['controllers']['progress']['wizard_name'] = lang('software_updates_app_name');
$app['controllers']['progress']['wizard_description'] = lang('software_updates_progress_help');
$app['controllers']['progress']['inline_help'] = array(
    lang('network_you_can_change_your_mind_later') => lang('network_network_mode_help'),
    lang('network_best_practices') => lang('network_network_mode_best_practices_help'),
);

/////////////////////////////////////////////////////////////////////////////
// Packaging
/////////////////////////////////////////////////////////////////////////////

$app['requires'] = array(
    'app-network',
);

$app['core_requires'] = array(
    'app-network-core',
);
