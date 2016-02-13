<?php

/*
 *  Module: webNpro Menu Editor v1.0
 *  Copyright: Kőrösi Zoltán | webNpro - hosting and design | http://webnpro.com | info@webnpro.com
 *  Contact / Feature request: info@webnpro.com (Hungarian / English)
 *  License: Please check CodeCanyon.net for license details.
 *  More license clarification available here:  http://codecanyon.net/wiki/support/legal-terms/licensing-terms/
 */

// Update the plugin and go back to the plugins settings page
require_once(dirname(__FILE__) . '/../update/updateclass.php');
// Get license infos
$update = new update;
echo $update->update();
unset($update);
