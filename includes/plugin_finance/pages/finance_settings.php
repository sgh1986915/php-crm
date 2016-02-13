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


if(!module_config::can_i('view','Settings')){
    redirect_browser(_BASE_HREF);
}

module_config::print_settings_form(array(
    'heading' => array(
        'title' => 'Dashboard Finance Settings',
        'main' => true,
        'type' => 'h2',
    ),
    'settings' => array(
         array(
            'key'=>'dashboard_income_summary',
            'default'=>1,
             'type'=>'checkbox',
             'description'=>'Show income summary on the dashboard.',
         ),
    )
)
);
