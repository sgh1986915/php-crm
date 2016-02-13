<?php 
/** 
  * Copyright: dtbaker 2012
  * Licence: Please check CodeCanyon.net for licence details. 
  * More licence clarification available here:  http://codecanyon.net/wiki/support/legal-terms/licensing-terms/ 
  * Deploy: 321 52e20b549dde146818983ece66d81a11
  * Envato: f2874e84-c8f9-4c6c-894f-2c79a77bf602
  * Package Date: 2012-05-29 04:20:08 
  * IP Address: 127.0.0.1
  */ 

if(isset($_REQUEST['report_id'])){

	include("report_admin_edit.php");

}else{ 
	
	include("report_admin_list.php");
	
} 

