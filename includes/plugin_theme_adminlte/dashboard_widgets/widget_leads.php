<?php 
/** 
  * Copyright: dtbaker 2012
  * Licence: Please check CodeCanyon.net for licence details. 
  * More licence clarification available here:  http://codecanyon.net/wiki/support/legal-terms/licensing-terms/ 
  * Deploy: 9809 f200f46c2a19bb98d112f2d32a8de0c4
  * Envato: 4ffca17e-861e-4921-86c3-8931978c40ca, 0a3014a3-2b8f-460b-8850-d6025aa845f8
  * Package Date: 2015-11-25 03:08:08 
  * IP Address: 67.79.165.254
  */
if(class_exists('module_customer',false) && module_security::can_user(module_security::get_loggedin_id(),'Show Dashboard Widgets')){
	$customer_types = module_customer::get_customer_types();
	foreach($customer_types as $customer_type){
		// if(!empty($customer_type['type_name_plural']) && $customer_type['customer_type_id']) { // dtbaker
		if(!empty($customer_type['type_name_plural'])) { // dtbaker
			if ( module_customer::can_i( 'view', $customer_type['type_name_plural'] ) ) {
				// find out how many open customers are left..
				$customers = module_customer::get_customers(array(
					'customer_type_id' => $customer_type['customer_type_id'],
				),true);
				ob_start();
				// icons from http://ionicons.com/
				?>

				<div class="small-box bg-yellow">
					<div class="inner">
						<h3>
							<?php echo mysql_num_rows($customers); ?>
						</h3>
						<p>
							<?php _e('Current %s', htmlspecialchars($customer_type['type_name_plural']));?>
						</p>
					</div>
					<div class="icon"><i class="fa fa-<?php echo htmlspecialchars($customer_type['menu_icon'] ? $customer_type['menu_icon'] : 'users');?>"></i></div>
					<a href="<?php $link = module_customer::link_open(false); echo $link . (strpos($link,'?') ? '&' : '?') .'customer_type_id='.$customer_type['customer_type_id'];?>" class="small-box-footer">
						<?php _e('View %s',htmlspecialchars($customer_type['type_name_plural']));?> <i class="fa fa-arrow-circle-right"></i>
					</a>
				</div>

				<?php
				$widgets[] = array(
					'id'      => 'open_customers_'.$customer_type['customer_type_id'],
					'columns' => 4,
					'raw' => true,
					'content' => ob_get_clean(),
				);
			}
		}
	}
}
