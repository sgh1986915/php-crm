<?php

$body = @file_get_contents("php://input");
$json_data = json_decode($body);

$callername = $json_data->callername;
$callernum = $json_data->callernum;
$callercity = $json_data->callercity;
$callerstate = $json_data->callerstate;
$callerzip = $json_data->callerzip;
$referrermedium = $json_data->referrermedium;
$callsource = $json_data->callsource;
$utm_campaign = $json_data->utm_campaign;
$utm_content = $json_data->utm_content;
$utm_term = $json_data->utm_term;
$keywords = $json_data->keywords;
$last_requested_url = $json_data->last_requested_url;
$ip = $json_data->ip;
$datetime = $json_data->datetime;
$trackingnum = $json_data->trackingnum;

$customer_import = array(
	'customer_name' => $callername,
	'customer_extra' => array(
		'Medium' => $referrermedium,
		'Source' => $callsource,
		'Campaign' => $utm_campaign,
		'Content' => $utm_content,
		'Term' => $utm_term,
		'Query' => $keywords,
		'Conversion URL' => $last_requested_url,
		'IP Address' => $ip,
		'Called In' => $datetime,
	),
	'address' => array(
		'line_1' => '123 Test Street',
		'line_2' => '',
		'suburb' => $callercity,
		'state' => $callerstate,
		'post_code' => $callerzip,
	),
	'contact' => array(
		'name' => $callername,
		'last_name' => $callername,
		'email' => $trackingnum,
		'mobile' => $callernum,
	),
);

include('init.php'); // the UCM init code.
$customer_id = $plugins['customer']->save_customer('new',array(
	'customer_name' => $customer_import['customer_name']
));
if(!$customer_id){
	echo 'Failed to create customer';
	exit;
}
if(!empty($customer_import['customer_extra'])) {
	foreach ( $customer_import['customer_extra'] as $extra_key => $extra_val ) {
		// Add the Medium extra field to that newly created customer
		$extra_db = array(
			'extra_key'   => $extra_key,
			'extra'       => $extra_val,
			'owner_table' => 'customer',
			'owner_id'    => $customer_id,
		);
		$extra_id = update_insert( 'extra_id', false, 'extra', $extra_db );
	}
}
if(!empty($customer_import['address'])) {
	// Save the address for the customer
	$customer_import['address']['owner_id'] = $customer_id;
	$customer_import['address']['owner_table'] = 'customer';
	$customer_import['address']['address_type'] = 'physical';
	module_address::save_address( false, $customer_import['address'] );
}
if(!empty($customer_import['contact'])) {
	// add the contact details to this customer record
	$customer_import['contact']['customer_id'] = $customer_id;
	$contact_user_id = $plugins['user']->create_user( $customer_import['contact'], 'signup' );
	if($contact_user_id){
		module_customer::set_primary_user_id($customer_id, $contact_user_id);
	}
}
echo "Created a customer with ID $customer_id and a contact with ID $contact_user_id ";