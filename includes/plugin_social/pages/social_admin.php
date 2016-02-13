<!-- show a list of tabs for all the different social methods, as menu hooks -->

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

$module->page_title = _l('Social');


$links = array();
if(module_social::can_i('view','Combined Comments','Social','social')){
	$links [] = array(
        "name"=>_l('Inbox'),
        'm' => 'social',
        'p' => 'social_messages',
		'args' => array(
			'combined' => 1,
			'social_twitter_id' => false,
			'social_facebook_id' => false,
		),
        'force_current_check' => true,
        //'current' => isset($_GET['combined']),
        'order' => 1, // at start
        'menu_include_parent' => 0,
        'allow_nesting' => 1,
    );

	//if(isset($_GET['combined'])){
	//	include('social_messages.php');
	//}
}