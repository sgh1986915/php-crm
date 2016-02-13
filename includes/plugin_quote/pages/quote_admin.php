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

$quote_safe = true; // stop including files directly.
if(!module_quote::can_i('view','Quotes')){
    echo 'permission denied';
    return;
}

if(isset($_REQUEST['quote_id'])){

    if(isset($_REQUEST['email_staff'])){
        include(module_theme::include_ucm("includes/plugin_quote/pages/quote_admin_email_staff.php"));

    }else if(isset($_REQUEST['email'])){
        include(module_theme::include_ucm("includes/plugin_quote/pages/quote_admin_email.php"));

    }else if((int)$_REQUEST['quote_id'] > 0){
        include(module_theme::include_ucm("includes/plugin_quote/pages/quote_admin_edit.php"));
        //include("quote_admin_edit.php");
    }else{
        include(module_theme::include_ucm("includes/plugin_quote/pages/quote_admin_create.php"));
        //include("quote_admin_create.php");
    }

}else{

    include(module_theme::include_ucm("includes/plugin_quote/pages/quote_admin_list.php"));
	//include("quote_admin_list.php");
	
} 

