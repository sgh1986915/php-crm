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

if(!$module->can_i('view','Products') || !$module->can_i('edit','Products')){
    redirect_browser(_BASE_HREF);
}

$module->page_title = 'Product Settings';

$links = array(
    array(
        "name"=>'Products',
        'm' => 'product',
        'p' => 'product_admin',
        'force_current_check' => true,
        'order' => 1, // at start.
        'menu_include_parent' => 1,
        'allow_nesting' => 1,
        'args'=>array('product_id'=>false),
    ),
    array(
        "name"=>'Categories',
        'm' => 'product',
        'p' => 'product_admin_category',
        'force_current_check' => true,
        'order' => 2, // at start.
        'menu_include_parent' => 1,
        'allow_nesting' => 1,
        'args'=>array('product_id'=>false,'product_category_id'=>false),
    ),
);

