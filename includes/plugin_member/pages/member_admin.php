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


if(isset($_REQUEST['member_id'])){
    $links = array();

    $member_id = $_REQUEST['member_id'];
    if($member_id && $member_id != 'new'){
        $member = module_member::get_member($member_id);
        // we have to load the menu here for the sub plugins under member
        // set default links to show in the bottom holder area.

        array_unshift($links,array(
            "name"=>_l('Member:').' <strong>'.htmlspecialchars($member['first_name'] .' '.$member['last_name']).'</strong>',
            "icon"=>"images/icon_arrow_down.png",
            'm' => 'member',
            'p' => 'member_admin',
            'default_page' => 'member_admin_edit',
            'order' => 1,
            'menu_include_parent' => 0,
        ));
    }else{
        $member = array(
            'name' => 'New Member',
        );
        array_unshift($links,array(
            "name"=>"New Member Details",
            "icon"=>"images/icon_arrow_down.png",
            'm' => 'member',
            'p' => 'member_admin',
            'default_page' => 'member_admin_edit',
            'order' => 1,
            'menu_include_parent' => 0,
        ));
    }

}else{
    include('member_admin_list.php');
}