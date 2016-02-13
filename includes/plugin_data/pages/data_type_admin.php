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

if(module_data::can_i('edit',_MODULE_DATA_NAME)){

	// show all datas.
	if(isset($_REQUEST['data_field_id']) && $_REQUEST['data_field_id'] && isset($_REQUEST['data_type_id']) && $_REQUEST['data_type_id']){
		
		include("data_type_admin_field_open.php");
		
	}else if(isset($_REQUEST['data_field_group_id']) && $_REQUEST['data_field_group_id']){
		
		include("data_type_admin_group_open.php");
		
	}else if(isset($_REQUEST['data_type_id']) && $_REQUEST['data_type_id']){
		
		include("data_type_admin_open.php");
		
	}else{
		
		include("data_type_admin_list.php");
	}
	
}