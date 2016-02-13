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

print_heading('Help Settings');

module_config::print_settings_form(
    array(
        array(
            'key'=>'help_only_for_admin',
            'default'=>1,
            'type'=>'checkbox',
            'description'=>'Only show help menu for Super Administrator.',
	        'help' => 'By default only the Super Administrator (first user created) can see the help documentation. If this option is disabled you will still need to give each User Role access to "view help" for them to see the "help" menu correctly. Please note that the help documentation may contain branding.'
        ),
    )
);
