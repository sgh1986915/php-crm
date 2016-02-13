<?php

/*
 *  Module: webNpro Menu Editor v1.0
 *  Copyright: Kőrösi Zoltán | webNpro - hosting and design | http://webnpro.com | info@webnpro.com
 *  Contact / Feature request: info@webnpro.com (Hungarian / English)
 *  License: Please check CodeCanyon.net for license details.
 *  More license clarification available here:  http://codecanyon.net/wiki/support/legal-terms/licensing-terms/
 */

// Get the infos about the plugin
$info = parse_ini_file(dirname(__FILE__) . '/../plugin.info');
$plugin_full_name = $info['fullname'];
$plugin_name = $info['modulename'];
$plugin_id = $info['id'];
$plugin_ver = $info['version'];
$plugin_documentation = $info['documentation'];

// Print the header and the go back button
$module->page_title = _l($plugin_full_name) . ' ' . _l('Documentation');
$header_buttons = array();

$header_buttons[] = array(
    'url' => 'javascript:history.go(-1)',
    'title' => _l('Go back')
);

$title = _l('You will be redirected to the documentation page in few seconds...');

print_heading(array(
    'main' => true,
    'type' => 'h2',
    'title' => $title,
    'button' => $header_buttons,
));

header("Refresh: 0;url=" . $plugin_documentation);
?>