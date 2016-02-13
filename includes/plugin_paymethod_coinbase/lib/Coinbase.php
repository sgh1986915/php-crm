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

if(!function_exists('curl_init')) {
    throw new Exception('The Coinbase client library requires the CURL PHP extension.');
}

require_once(dirname(__FILE__) . '/Coinbase/Exception.php');
require_once(dirname(__FILE__) . '/Coinbase/ApiException.php');
require_once(dirname(__FILE__) . '/Coinbase/ConnectionException.php');
require_once(dirname(__FILE__) . '/Coinbase/Coinbase.php');
require_once(dirname(__FILE__) . '/Coinbase/Requestor.php');
require_once(dirname(__FILE__) . '/Coinbase/Rpc.php');
require_once(dirname(__FILE__) . '/Coinbase/OAuth.php');
require_once(dirname(__FILE__) . '/Coinbase/TokensExpiredException.php');
require_once(dirname(__FILE__) . '/Coinbase/Authentication.php');
require_once(dirname(__FILE__) . '/Coinbase/SimpleApiKeyAuthentication.php');
require_once(dirname(__FILE__) . '/Coinbase/OAuthAuthentication.php');
require_once(dirname(__FILE__) . '/Coinbase/ApiKeyAuthentication.php');
