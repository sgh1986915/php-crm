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

class Stripe_Util_Set
{
  private $_elts;

  public function __construct($members=array())
  {
    $this->_elts = array();
    foreach ($members as $item)
      $this->_elts[$item] = true;
  }

  public function includes($elt)
  {
    return isset($this->_elts[$elt]);
  }

  public function add($elt)
  {
    $this->_elts[$elt] = true;
  }

  public function discard($elt)
  {
    unset($this->_elts[$elt]);
  }

  // TODO: make Set support foreach
  public function toArray()
  {
    return array_keys($this->_elts);
  }
}
