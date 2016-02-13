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


$ticket_safe = true;
$limit_time = strtotime('-'.module_config::c('ticket_turn_around_days',5).' days',time());


if(isset($_REQUEST['ticket_id'])){

    if(isset($_REQUEST['notify'])&&$_REQUEST['notify']){
        include(module_theme::include_ucm("includes/plugin_ticket/pages/ticket_admin_notify.php"));
    }else{
        include(module_theme::include_ucm("includes/plugin_ticket/pages/ticket_admin_edit.php"));
    }
    //include('ticket_admin_edit.php');


    /*if(module_security::getlevel() > 1){
        ob_end_clean();
        $_REQUEST['i'] = $_REQUEST['ticket_id'];
        $_REQUEST['hash'] = module_ticket::link_public($_REQUEST['ticket_id'],true);
        $module->external_hook('public');
        exit;
        //include('includes/plugin_ticket/public/ticket_customer_view.php');
    }else{*/
    //include("ticket_admin_edit.php");
    //}

}else{



    //include("ticket_admin_list.php");
    include(module_theme::include_ucm("includes/plugin_ticket/pages/ticket_admin_list.php"));

}
