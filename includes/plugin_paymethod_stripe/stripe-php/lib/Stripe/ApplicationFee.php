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

class Stripe_ApplicationFee extends Stripe_ApiResource
{
  public static function className($class)
  {
    return 'application_fee';
  }

  public static function constructFrom($values, $apiKey=null)
  {
    $class = get_class();
    return self::scopedConstructFrom($class, $values, $apiKey);
  }

  public static function retrieve($id, $apiKey=null)
  {
    $class = get_class();
    return self::_scopedRetrieve($class, $id, $apiKey);
  }

  public static function all($params=null, $apiKey=null)
  {
    $class = get_class();
    return self::_scopedAll($class, $params, $apiKey);
  }

  public function refund($params=null)
  {
    $requestor = new Stripe_ApiRequestor($this->_apiKey);
    $url = $this->instanceUrl() . '/refund';
    list($response, $apiKey) = $requestor->request('post', $url, $params);
    $this->refreshFrom($response, $apiKey);
    return $this;
  }
}
