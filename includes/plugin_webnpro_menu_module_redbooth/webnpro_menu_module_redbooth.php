<?php

/*
 *  Module: webNpro Menu Editor v1.0
 *  Copyright: Kőrösi Zoltán | webNpro - hosting and design | http://webnpro.com | info@webnpro.com
 *  Contact / Feature request: info@webnpro.com (Hungarian / English)
 *  License: Please check CodeCanyon.net for license details.
 *  More licence clarification available here:  http://codecanyon.net/wiki/support/legal-terms/licensing-terms/
 *
 *  THIS IS A CUSTOM MENU ITEM MODULE CREATED BY THE MENU EDITOR PLUGIN
 */

class module_webnpro_menu_module_redbooth extends module_base {
public $links;
public function init() {
$this->links = array();
$this->module_name = "webnpro_menu_module_redbooth";
$this->module_position = '13';
$this->version = '1.0';
module_config::save_config('_menu_order_webnpro_menu_module_redbooth', '13');
}

public function pre_menu() {
global $load_modules;
$this->links = array(
array(
'name' => 'Redbooth',
 'url' => 'https://redbooth.com/',
 'icon_name' => 'star',
 'order' => '13'
)
);
}
}

