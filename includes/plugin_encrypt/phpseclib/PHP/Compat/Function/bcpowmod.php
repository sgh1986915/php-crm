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
// $Id: bcpowmod.php,v 1.1 2007-07-02 04:19:55 terrafrost Exp $


/**
 * Replace bcpowmod()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @license     LGPL - http://www.gnu.org/licenses/lgpl.html
 * @copyright   2004-2007 Aidan Lister <aidan@php.net>, Arpad Ray <arpad@php.net>
 * @link        http://php.net/function.bcpowmod
 * @author      Sara Golemon <pollita@php.net>
 * @version     $Revision: 1.1 $
 * @since       PHP 5.0.0
 * @require     PHP 4.0.0 (user_error)
 */
function php_compat_bcpowmod($x, $y, $modulus, $scale = 0)
{
    // Sanity check
    if (!is_scalar($x)) {
        user_error('bcpowmod() expects parameter 1 to be string, ' .
            gettype($x) . ' given', E_USER_WARNING);
        return false;
    }

    if (!is_scalar($y)) {
        user_error('bcpowmod() expects parameter 2 to be string, ' .
            gettype($y) . ' given', E_USER_WARNING);
        return false;
    }

    if (!is_scalar($modulus)) {
        user_error('bcpowmod() expects parameter 3 to be string, ' .
            gettype($modulus) . ' given', E_USER_WARNING);
        return false;
    }

    if (!is_scalar($scale)) {
        user_error('bcpowmod() expects parameter 4 to be integer, ' .
            gettype($scale) . ' given', E_USER_WARNING);
        return false;
    }

    $t = '1';
    while (bccomp($y, '0')) {
        if (bccomp(bcmod($y, '2'), '0')) {
            $t = bcmod(bcmul($t, $x), $modulus);
            $y = bcsub($y, '1');
        }

        $x = bcmod(bcmul($x, $x), $modulus);
        $y = bcdiv($y, '2');
    }

    return $t;    
}


// Define
if (!function_exists('bcpowmod')) {
    function bcpowmod($x, $y, $modulus, $scale = 0)
    {
        return php_compat_bcpowmod($x, $y, $modulus, $scale);
    }
}