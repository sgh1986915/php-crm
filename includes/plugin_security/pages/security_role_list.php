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

if(!module_config::can_i('view','Settings') || !module_security::can_i('view','Security Roles','Security')){
    redirect_browser(_BASE_HREF);
}
$search = (isset($_REQUEST['search']) && is_array($_REQUEST['search'])) ? $_REQUEST['search'] : array();
$roles = $module->get_roles($search);

$header = array(
    'type' => 'h2',
    'title' => _l('Security Roles'),
    'main' => true,
    'button' => array(
        'title' => 'Add New Role',
        'type' => 'add',
        'url' => module_security::link_open_role('new'),
    )
);
print_heading($header);
?>


<form action="" method="post">


<?php

/** START TABLE LAYOUT **/
    $table_manager = module_theme::new_table_manager();
    $columns = array();
    $columns['name'] = array(
            'title' => 'Name',
            'callback' => function($role) use (&$module){
                echo $module->link_open_role($role['security_role_id'],true);
            },
            'cell_class' => 'row_action',
        );
    $table_manager->set_columns($columns);
    $table_manager->set_rows($roles);
    $table_manager->pagination = true;
    $table_manager->print_table();

?>
</form>