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

$page_type = 'Active Leads';
$page_type_single = 'Active Lead';

$current_customer_type_id = module_customer::get_current_customer_type_id();
if($current_customer_type_id > 0){
	$customer_type = module_customer::get_customer_type($current_customer_type_id);
	if($customer_type && !empty($customer_type['type_name'])){
		$page_type = $customer_type['type_name_plural'];
		$page_type_single = $customer_type['type_name'];
	}
}


$module->page_title = $page_type_single;

if(!module_customer::can_i('view',$page_type)){
    redirect_browser(_BASE_HREF);
}

if(isset($customer_id)){
	// we're coming here a second time
}
$links = array();

$customer_id = $_REQUEST['customer_id'];
if($customer_id && $customer_id != 'new'){
	$customer = module_customer::get_customer($customer_id);
	// we have to load the menu here for the sub plugins under customer
	// set default links to show in the bottom holder area.

    if(!$customer || $customer['customer_id'] != $customer_id){
        redirect_browser('');
    }
    $class = '';
    if(isset($customer['customer_status'])){
         switch($customer['customer_status']){
             case _CUSTOMER_STATUS_OVERDUE:
                 $class = 'customer_overdue error_text';
                 break;
             case _CUSTOMER_STATUS_OWING:
                 $class = 'customer_owing';
                 break;
             case _CUSTOMER_STATUS_PAID:
                 $class = 'customer_paid success_text';
                 break;
         }
    }
	array_unshift($links,array(
		"name"=>_l(''.$page_type_single.': %s','<strong class="'.$class.'">'.htmlspecialchars($customer['customer_name']).'</strong>'),
		"icon"=>"images/icon_arrow_down.png",
		'm' => 'customer',
		'p' => 'customer_admin_open',
		'default_page' => 'customer_admin_edit',
		'order' => 1,
		'menu_include_parent' => 0,
	));
}else{
	$customer = array(
		'name' => 'New '.$page_type_single,
	);
	array_unshift($links,array(
		"name"=>'New '.$page_type_single.' Details',
		"icon"=>"images/icon_arrow_down.png",
		'm' => 'customer',
		'p' => 'customer_admin_open',
		'default_page' => 'customer_admin_edit',
		'order' => 1,
		'menu_include_parent' => 0,
	));
}
