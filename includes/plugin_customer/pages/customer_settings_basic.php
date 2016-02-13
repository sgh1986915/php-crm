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

if(!module_customer::can_i('edit','Customer Settings','Config')){
	redirect_browser(_BASE_HREF);
}

ob_start();
$customer_templates = array();
$customer_templates['customer_statement_email'] = 1;
foreach($customer_templates as $template_key => $tf){
	module_template::link_open_popup($template_key);
}
$template_html = ob_get_clean();


$settings = array(
	array(
		'key'=>'customer_staff_name',
		'default'=>'Staff',
		'type'=>'text',
		'description'=>'Customer Staff Name',
		'help'=>'What are customer staff members called? e.g. "Staff" or "Team Leader" or "Admin"',
	),
	array(
		'key'=>'customer_list_show_invoices',
		'default'=>'1',
		'type'=>'checkbox',
		'description'=>'Show Invoices in Customer List',
		'help'=>'If invoices should be shown in the main customer listing. If you have lots of customers and lots of invoices you can try disable this option to speed things up a bit.',
	),
	array(
		'key'=>'customer_staff_list',
		'default'=>'1',
		'type'=>'checkbox',
		'description'=>'Show Staff in Customer List',
		'help'=>'Enable this option to show staff members in the main customer listing area',
	),
	array(
		'type'=>'html',
		'description'=>'Templates',
		'html' => $template_html,
	),
);


module_config::print_settings_form(
	array(
		'heading' => array(
			'title' => 'Customer Settings',
			'type' => 'h2',
			'main' => true,
		),
		'settings' => $settings,
	)
);