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

$file_safe = true;
$file_id = isset($_REQUEST['file_id']) ? (int)$_REQUEST['file_id'] : false;

if($file_id && isset($_REQUEST['email'])){

    include(module_theme::include_ucm('includes/plugin_file/pages/file_admin_email.php'));

}else if(isset($_REQUEST['file_id'])){


	$ucm_file = new ucm_file( $file_id );
	$ucm_file->check_page_permissions();
	$file    = $ucm_file->get_data();
	$file_id = (int) $file['file_id']; // sanatisation/permission check

	if(isset($_REQUEST['bucket']) || (isset($file['bucket']) && $file['bucket'])){
	    include(module_theme::include_ucm('includes/plugin_file/pages/file_admin_bucket.php'));
	}else{
		include(module_theme::include_ucm('includes/plugin_file/pages/file_admin_edit.php'));
	}


}else{
	
    include(module_theme::include_ucm('includes/plugin_file/pages/file_admin_list.php'));
	
} 

