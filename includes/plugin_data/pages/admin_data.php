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


// show all datas.
if(isset($_REQUEST['search_form'])){

	include("admin_data_search.php");

}else if(isset($_REQUEST['data_new'])){

	include("admin_data_new.php");
	
}else if(isset($_REQUEST['data_record_id']) && $_REQUEST['data_record_id'] ){
	//&& isset($_REQUEST['data_type_id']) && $_REQUEST['data_type_id']
	
	include("admin_data_open.php");
	
}else{
	
	include("admin_data_list.php");
}

