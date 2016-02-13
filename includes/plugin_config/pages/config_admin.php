
<?php 
/** 
  * Copyright: dtbaker 2012
  * Licence: Please check CodeCanyon.net for licence details. 
  * More licence clarification available here:  http://codecanyon.net/wiki/support/legal-terms/licensing-terms/ 
  * Deploy: 9809 f200f46c2a19bb98d112f2d32a8de0c4
  * Envato: 4ffca17e-861e-4921-86c3-8931978c40ca
  * Package Date: 2015-11-25 02:55:20 
  * IP Address: 67.79.165.254
  */

$module->page_title = 'Admin';

if(module_config::can_i('view','Settings')){
    $links = array(
        array(
            "name"=>"System Settings",
            'm' => 'config',
            'p' => 'config_basic_settings',
            'force_current_check' => true,
            //'default_page' => 'config_basic_settings',
            'order' => 1, // at start.
            'menu_include_parent' => 0,
            'allow_nesting' => 1,
        ),
        array(
            "name"=>"Menu Order",
            'm' => 'config',
            'p' => 'config_menu',
            'force_current_check' => true,
            'order' => 9994,
            'menu_include_parent' => 0,
            'allow_nesting' => 1,
        ),
        array(
            "name"=>"Payments",
            'm' => 'config',
            'p' => 'config_payment',
            'force_current_check' => true,
            'order' => 9995,
            'menu_include_parent' => 0,
            'allow_nesting' => 1,
        ),
        array(
            "name"=>"Advanced",
            'm' => 'config',
            'p' => 'config_settings',
            'force_current_check' => true,
            //'default_page' => 'config_settings',
            'order' => 9999, // at end.
            'menu_include_parent' => 0,
            'allow_nesting' => 1,
        ),
    );
}

if(module_config::can_i('view','Upgrade System')){
    $links[] = array(
        "name"=>"Upgrade",
            'm' => 'config',
            'p' => 'config_upgrade',
            'force_current_check' => true,
            'order' => 9998, // at end.
            'menu_include_parent' => 0,
            'allow_nesting' => 1,
        );
}

?>