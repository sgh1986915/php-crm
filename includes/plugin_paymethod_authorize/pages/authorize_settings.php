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


if(!module_config::can_i('edit','Settings')){
    redirect_browser(_BASE_HREF);
}

print_heading('Authorize Settings');?>


<?php module_config::print_settings_form(
    array(
         array(
            'key'=>'payment_method_authorize_enabled',
            'default'=>1,
             'type'=>'checkbox',
             'description'=>'Enable Authorize Checkout',
         ),
         array(
            'key'=>'payment_method_authorize_enabled_default',
            'default'=>1,
             'type'=>'checkbox',
             'description'=>'Available By Default On Invoices',
	         'help' => 'If this option is enabled, all new invoices will have this payment method available. If this option is disabled, it will have to be enabled on individual invoices.'
         ),
         array(
            'key'=>'payment_method_authorize_sandbox',
            'default'=>0,
             'type'=>'checkbox',
             'description'=>'Enable Sandbox Mode',
         ),
         array(
            'key'=>'payment_method_authorize_api_login_id',
            'default'=>'',
             'type'=>'text',
             'description'=>'Your Authorize API Login ID',
         ),
         array(
            'key'=>'payment_method_authorize_transaction_key',
            'default'=>'',
             'type'=>'text',
             'description'=>'Your Authorize Transaction Key',
         ),
    )
); ?>

<?php print_heading('Authorize setup instructions:');?>

<p>Authorize.net only supports payments in USD. Please make sure all your invoices are in USD to use Authorize.net</p>
<p>Please signup for a Authorize account here: https://authorize.net - please enter your authorize API Keys above.</p>
<p>For testing select "Visa" and enter the test credit card number 4111111111111111, any expiration date (MMYY) in the future (such as "1120"), and hit "Submit".</p>
<p><strong>Please use SSL on your website (eg: https://) in order to use Authorize.net</strong></p>
<p>To change how the Authorize credit card form looks please go to Settings > Templates and look for 'authorize_credit_card_form'.</p>
<p>If you get "cannot connect" errors when making a payment please try going to Settings > Advanced and changing 'payment_method_authorize_ssl_verify' from 1 to 0</p>