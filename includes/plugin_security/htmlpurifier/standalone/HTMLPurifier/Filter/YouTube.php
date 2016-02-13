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

class HTMLPurifier_Filter_YouTube extends HTMLPurifier_Filter
{

    public $name = 'YouTube';

    public function preFilter($html, $config, $context) {
        $pre_regex = '#<object[^>]+>.+?'.
            'http://www.youtube.com/((?:v|cp)/[A-Za-z0-9\-_=]+).+?</object>#s';
        $pre_replace = '<span class="youtube-embed">\1</span>';
        return preg_replace($pre_regex, $pre_replace, $html);
    }

    public function postFilter($html, $config, $context) {
        $post_regex = '#<span class="youtube-embed">((?:v|cp)/[A-Za-z0-9\-_=]+)</span>#';
        return preg_replace_callback($post_regex, array($this, 'postFilterCallback'), $html);
    }

    protected function armorUrl($url) {
        return str_replace('--', '-&#45;', $url);
    }

    protected function postFilterCallback($matches) {
        $url = $this->armorUrl($matches[1]);
        return '<object width="425" height="350" type="application/x-shockwave-flash" '.
            'data="http://www.youtube.com/'.$url.'">'.
            '<param name="movie" value="http://www.youtube.com/'.$url.'"></param>'.
            '<!--[if IE]>'.
            '<embed src="http://www.youtube.com/'.$url.'"'.
            'type="application/x-shockwave-flash"'.
            'wmode="transparent" width="425" height="350" />'.
            '<![endif]-->'.
            '</object>';

    }
}

// vim: et sw=4 sts=4
