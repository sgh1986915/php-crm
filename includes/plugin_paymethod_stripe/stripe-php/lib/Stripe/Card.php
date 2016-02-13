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

class Stripe_Card extends Stripe_ApiResource
{
  public static function constructFrom($values, $apiKey=null)
  {
    $class = get_class();
    return self::scopedConstructFrom($class, $values, $apiKey);
  }

  public function instanceUrl()
  {
    $id = $this['id'];
    $customer = $this['customer'];
    $class = get_class($this);
    if (!$id) {
      throw new Stripe_InvalidRequestError("Could not determine which URL to request: $class instance has invalid ID: $id", null);
    }
    $id = Stripe_ApiRequestor::utf8($id);
    $customer = Stripe_ApiRequestor::utf8($customer);

    $base = self::classUrl('Stripe_Customer');
    $customerExtn = urlencode($customer);
    $extn = urlencode($id);
    return "$base/$customerExtn/cards/$extn";
  }

  public function delete($params=null)
  {
    $class = get_class();
    return self::_scopedDelete($class, $params);
  }

  public function save()
  {
    $class = get_class();
    return self::_scopedSave($class);
  }
}