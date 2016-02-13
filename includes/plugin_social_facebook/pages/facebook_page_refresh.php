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

if(!module_social::can_i('edit','Facebook','Social','social')){
    die('No access to Facebook accounts');
}

$social_facebook_id = isset($_REQUEST['social_facebook_id']) ? (int)$_REQUEST['social_facebook_id'] : 0;
$facebook = new ucm_facebook_account($social_facebook_id);

$facebook_page_id = isset($_REQUEST['facebook_page_id']) ? (int)$_REQUEST['facebook_page_id'] : 0;

/* @var $pages ucm_facebook_page[] */
$pages = $facebook->get('pages');
if(!$facebook_page_id || !$pages || !isset($pages[$facebook_page_id])){
	die('No pages found to refresh');
}
?>
Manually refreshing page data...
<?php

$pages[$facebook_page_id]->graph_load_latest_page_data();
