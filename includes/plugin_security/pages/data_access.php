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

$access = true;


switch($table_name){
    case 'invoice':
    default:
        // check if current user can access this invoice.
        if($data && isset($data['customer_id']) && (int)$data['customer_id']>0){
            $valid_customer_ids = module_security::get_customer_restrictions();
            if($valid_customer_ids){
                $access = isset($valid_customer_ids[$data['customer_id']]);
                if(!$access)return false;
            }
        }
        break;
}