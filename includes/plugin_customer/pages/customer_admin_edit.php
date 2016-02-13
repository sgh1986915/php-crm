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

if(isset($_REQUEST['email'])){
	include(module_theme::include_ucm('includes/plugin_customer/pages/customer_admin_email.php'));
	return;
}

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

if(!module_customer::can_i('view',$page_type)){
    redirect_browser(_BASE_HREF);
}


$customer_id = (int)$_REQUEST['customer_id'];
$customer = array();

$customer = module_customer::get_customer($customer_id);

if($customer_id>0 && $customer['customer_id']==$customer_id){
    $module->page_title = _l($page_type_single.': %s',$customer['customer_name']);
}else{
    $module->page_title = _l($page_type_single.': %s',_l('New'));
}
// check permissions.
if(class_exists('module_security',false)){
    if($customer_id>0 && $customer['customer_id']==$customer_id){
        // if they are not allowed to "edit" a page, but the "view" permission exists
        // then we automatically grab the page and regex all the crap out of it that they are not allowed to change
        // eg: form elements, submit buttons, etc..
		module_security::check_page(array(
            'category' => 'Customer',
            'page_name' => $page_type,
            'module' => 'customer',
            'feature' => 'Edit',
		));
    }else{
		module_security::check_page(array(
			'category' => 'Customer',
            'page_name' => $page_type,
            'module' => 'customer',
            'feature' => 'Create',
		));
	}
	module_security::sanatise_data('customer',$customer);
}


?>
<form action="" method="post" id="customer_form">
	<input type="hidden" name="_process" value="save_customer" />
	<input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>" />
	<input type="hidden" name="_redirect" value="" id="form_redirect" />

    <?php
    $required = array(
	    'fields' => array(
		    'customer_name' => 'Name',
		    'name' => 'Contact Name',
	    ))
    ;
    if(module_config::c('user_email_required',1)){
	    $required['fields']['email'] = true;
    }
    module_form::set_required($required);
    module_form::prevent_exit(array(
        'valid_exits' => array(
            // selectors for the valid ways to exit this form.
            '.submit_button',
        ))
    );

    module_form::print_form_auth();

    //!(int)$customer['customer_id'] &&
    if(isset($_REQUEST['move_user_id']) && (int)$_REQUEST['move_user_id']>0 && module_customer::can_i('create','Customers')){
        // we have to move this contact over to this customer as a new primary user id
        $customer['primary_user_id'] = (int)$_REQUEST['move_user_id'];
        ?>
        <input type="hidden" name="move_user_id" value="<?php echo $customer['primary_user_id'];?>">
        <?php
    }

    hook_handle_callback('layout_column_half',1);

    /** COMPANY INFORMATION **/

    if(class_exists('module_company',false) && module_company::can_i('view','Company') && module_company::is_enabled()){
	    $responsive_summary = array();
        $companys = module_company::get_companys();
	    foreach($companys as $company){
		    if(isset($customer['company_ids'][$company['company_id']]) || (!$customer_id && !module_company::can_i('edit','Company'))){
			    $responsive_summary[] = htmlspecialchars($company['name']);
		    }
        }
        $heading = array(
            'type' => 'h3',
            'title' => 'Company Information',
	        'responsive' => array(
		        'title' => 'Company',
		        'summary' => implode(', ',$responsive_summary),
	        ),
        );
        if(module_company::can_i('edit','Company')){
            $help_text = addcslashes(_l("Here you can select which Company this Customer belongs to. This is handy if you are running multiple companies through this system and you would like to separate customers between different companies."),"'");
            $heading['button'] =  array(
              'url' => '#',
              'onclick' => "alert('$help_text'); return false;",
              'title' => 'help',
          );
        }
        //print_heading($heading);
        $company_fields = array();
        foreach($companys as $company){
            $company_fields[] = array(
                'type' => 'hidden',
                'name' => "available_customer_company[".$company['company_id']."]",
                'value' => 1,
            );
            $company_fields[] = array(
                'type' => 'check',
                'name' => "customer_company[".$company['company_id']."]",
                'value' => $company['company_id'],
                'checked' => isset($customer['company_ids'][$company['company_id']]) || (!$customer_id && !module_company::can_i('edit','Company')),
                'label' => htmlspecialchars($company['name']),
            );
        }
        $fieldset_data = array(
            'heading' => $heading,
            'class' => 'tableclass tableclass_form tableclass_full',
            'elements' => array(
                'company' => array(
                    'title' => _l('Company'),
                    'fields' => $company_fields,
                ),
            )
        );
        echo module_form::generate_fieldset($fieldset_data);
    }

    /** CUSTOMER INFORMATION **/

    $responsive_summary = array();
    $responsive_summary[] = htmlspecialchars($customer['customer_name']);
    $fieldset_data = array(
        'heading' => array(
            'type' => 'h3',
            'title' => $page_type_single.' Information',
	        'responsive' => array(
		        'title' => $page_type_single,
		        'summary' => implode(', ',$responsive_summary),
	        ),
        ),
        'class' => 'tableclass tableclass_form tableclass_full',
        'elements' => array(
            'name' => array(
                'title' => _l('Name'),
                'field' => array(
                    'type' => 'text',
                    'name' => 'customer_name',
                    'value' => $customer['customer_name'],
                ),
            ),
            'type' => array(
                'title' => _l('Type'),
                'ignore' => (!module_customer::get_customer_types()),
                'field' => array(
                    'type' => 'select',
                    'name' => 'customer_type_id',
                    'value' => $customer['customer_type_id'],
                    'blank' => false,
                    'options' => module_customer::get_customer_types(),
                    'options_array_id' => 'type_name',
                ),
            ),
        ),
    );
    if(class_exists('module_extra',false) && module_extra::is_plugin_enabled() && module_extra::can_i('view',$page_type)){
	    $fieldset_data['extra_settings'] = array(
            'owner_table' => 'customer',
            'owner_key' => 'customer_id',
            'owner_id' => $customer_id,
            'layout' => 'table_row',
            'allow_new' => module_extra::can_i('create',$page_type),
            'allow_edit' => module_extra::can_i('edit',$page_type),
        );
    }
  
    echo module_form::generate_fieldset($fieldset_data);
    unset($fieldset_data);



    /** PRIMARY CONTACT DETAILS **/

    // we use the "user" module to find the user details
    // for the currently selected primary contact id
    if($customer['primary_user_id']){

        if(!module_user::can_i('view','All '.$page_type_single.' Contacts','Customer','customer') && $customer['primary_user_id'] != module_security::get_loggedin_id()){
            ob_start();
            echo '<div class="content_box_wheader"><table width="100%" border="0" cellspacing="0" cellpadding="2" class="tableclass tableclass_form"><tbody><tr><td>';
            _e('Details hidden');
            echo '</td></tr></tbody></table></div>';
	        $responsive_summary = array();
		    $responsive_summary[] = htmlspecialchars($customer['customer_name']);
            $fieldset_data = array(
                'heading' => array(
                    'type' => 'h3',
                    'title' => 'Primary Contact Details',
			        'responsive' => array(
				        'title' => 'Primary Contact',
				        'summary' => implode(', ',$responsive_summary),
			        ),
                ),
                'class' => 'tableclass tableclass_form tableclass_full',
                'elements_before' => ob_get_clean(),
            );
            if($customer['primary_user_id']){
                $fieldset_data['heading']['button'] = array(
                    'title' => 'More',
                    'url' => module_user::link_open_contact($customer['primary_user_id'],false)
                );
            }
            echo module_form::generate_fieldset($fieldset_data);
            unset($fieldset_data);
        }else if(!module_user::can_i('edit','All '.$page_type_single.' Contacts','Customer','customer') && $customer['primary_user_id'] != module_security::get_loggedin_id()){
            // no permissions to edit.
            ob_start();
            echo '<div class="content_box_wheader"><table width="100%" border="0" cellspacing="0" cellpadding="2" class="tableclass tableclass_form"><tbody><tr><td>';
	        ob_start();
            module_user::print_contact_summary($customer['primary_user_id'],'text',array('name','last_name','email'));
	        $short_user_details = ob_get_clean();
            echo '</td></tr></tbody></table></div>';
            $fieldset_data = array(
                'heading' => array(
                    'type' => 'h3',
                    'title' => 'Primary Contact Details',
			        'responsive' => array(
				        'title' => 'Primary Contact',
				        'summary' => htmlspecialchars($short_user_details),
			        ),
                ),
                'class' => 'tableclass tableclass_form tableclass_full',
                'elements_before' => ob_get_clean(),
            );
            if($customer['primary_user_id']){
                $fieldset_data['heading']['button'] = array(
                    'title' => 'More',
                    'url' => module_user::link_open_contact($customer['primary_user_id'],false)
                );
            }
            echo module_form::generate_fieldset($fieldset_data);
            unset($fieldset_data);
        }else{
            module_user::print_contact_form($customer['primary_user_id']);
        }
    }else{
        // hack to create new contact details.
        module_user::print_contact_form(false);
    }


    /*** ADDRESS **/

    if(class_exists('module_address',false)){
        module_address::print_address_form($customer_id,'customer','physical','Address');
    }



    /** ADVANCED AREA **/

   


    hook_handle_callback('layout_column_half',2);


    if($customer_id && $customer_id!='new'){

        if(class_exists('module_group',false) && module_group::is_plugin_enabled()){
            module_group::display_groups(array(
                 'title' => $page_type_single.' Groups',
                'owner_table' => 'customer',
                'owner_id' => $customer_id,
                'view_link' => $module->link_open($customer_id),

            ));
        }

        $note_summary_owners = array();
        // generate a list of all possible notes we can display for this customer.
        // display all the notes which are owned by all the sites we have access to

        // display all the notes which are owned by all the users we have access to
        foreach(module_user::get_contacts(array('customer_id'=>$customer_id)) as $val){
            $note_summary_owners['user'][] = $val['user_id'];
        }
        if(class_exists('module_website',false) && module_website::is_plugin_enabled()){
            foreach(module_website::get_websites(array('customer_id'=>$customer_id)) as $val){
                $note_summary_owners['website'][] = $val['website_id'];
            }
        }
        if(class_exists('module_job',false) && module_job::is_plugin_enabled()){
            foreach(module_job::get_jobs(array('customer_id'=>$customer_id)) as $val){
                $note_summary_owners['job'][] = $val['job_id'];
                foreach(module_invoice::get_invoices(array('job_id'=>$val['job_id'])) as $val){
                    $note_summary_owners['invoice'][$val['invoice_id']] = $val['invoice_id'];
                }
            }
        }
        if(class_exists('module_invoice',false) && module_invoice::is_plugin_enabled()){
            foreach(module_invoice::get_invoices(array('customer_id'=>$customer_id)) as $val){
                $note_summary_owners['invoice'][$val['invoice_id']] = $val['invoice_id'];
            }
        }
        if(class_exists('module_note',false) && module_note::is_plugin_enabled()){
            module_note::display_notes(array(
                'title' => 'All '.$page_type_single.' Notes',
                'owner_table' => 'customer',
                'owner_id' => $customer_id,
                'view_link' => $module->link_open($customer_id),
                'display_summary' => true,
                'summary_owners' => $note_summary_owners
                )
            );
        }


    }
    hook_handle_callback('customer_edit',$customer_id);

    hook_handle_callback('layout_column_half','end');

    $form_actions = array(
        'class' => 'action_bar action_bar_center',
        'elements' => array(
            array(
                'type' => 'save_button',
                'name' => 'butt_save',
                'onclick' => "$('#form_redirect').val('".$module->link_open(false)."');",
                'value' => _l('Save and Return'),
            ),
            array(
                'type' => 'save_button',
                'name' => 'butt_save',
                'value' => _l('Save'),
            ),
            array(
                'ignore' => !(module_customer::can_i('delete','Customers') && $customer_id > 0),
                'type' => 'delete_button',
                'name' => 'butt_del',
                'value' => _l('Delete'),
            ),
            array(
                'type' => 'button',
                'name' => 'cancel',
                'value' => _l('Cancel'),
                'class' => 'submit_button',
                'onclick' => "window.location.href='".$module->link_open(false)."';",
            ),
        ),
    );
    echo module_form::generate_form_actions($form_actions);

    ?>



</form>

