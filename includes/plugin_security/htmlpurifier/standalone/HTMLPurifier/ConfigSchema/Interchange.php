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

/**
 * Generic schema interchange format that can be converted to a runtime
 * representation (HTMLPurifier_ConfigSchema) or HTML documentation. Members
 * are completely validated.
 */
class HTMLPurifier_ConfigSchema_Interchange
{

    /**
     * Name of the application this schema is describing.
     */
    public $name;

    /**
     * Array of Directive ID => array(directive info)
     */
    public $directives = array();

    /**
     * Adds a directive array to $directives
     */
    public function addDirective($directive) {
        if (isset($this->directives[$i = $directive->id->toString()])) {
            throw new HTMLPurifier_ConfigSchema_Exception("Cannot redefine directive '$i'");
        }
        $this->directives[$i] = $directive;
    }

    /**
     * Convenience function to perform standard validation. Throws exception
     * on failed validation.
     */
    public function validate() {
        $validator = new HTMLPurifier_ConfigSchema_Validator();
        return $validator->validate($this);
    }

}

// vim: et sw=4 sts=4
