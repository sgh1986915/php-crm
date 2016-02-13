<?php 
/** 
  * Copyright: dtbaker 2012
  * Licence: Please check CodeCanyon.net for licence details. 
  * More licence clarification available here:  http://codecanyon.net/wiki/support/legal-terms/licensing-terms/ 
  * Deploy: 9809 f200f46c2a19bb98d112f2d32a8de0c4
  * Envato: 4ffca17e-861e-4921-86c3-8931978c40ca, 0a3014a3-2b8f-460b-8850-d6025aa845f8
  * Package Date: 2015-11-25 03:08:08 
  * IP Address: 67.79.165.254
  */

if(!isset($_REQUEST['display_mode']) || (isset($_REQUEST['display_mode']) && $_REQUEST['display_mode']!='iframe' && $_REQUEST['display_mode']!='ajax')){
    $_REQUEST['display_mode'] = 'adminlte';
}
require_once(module_theme::include_ucm('includes/plugin_theme_adminlte/functions.php'));

module_config::register_css('theme','bootstrap.min.css',full_link('/includes/plugin_theme_adminlte/css/bootstrap.min.css'),11);
module_config::register_css('theme','font-awesome.min.css',full_link('/includes/plugin_theme_adminlte/css/font-awesome.min.css'),11);
module_config::register_css('theme','jquery.ui.min.css',full_link('/includes/plugin_theme_adminlte/css/jquery-ui-1.10.3.custom.css'),5);
//module_config::register_css('theme','jquery.ui.structure.min.css',full_link('/includes/plugin_theme_adminlte/css/jquery-ui.structure.min.css'),6);
//module_config::register_css('theme','jquery.ui.theme.min.css',full_link('/includes/plugin_theme_adminlte/css/jquery-ui.theme.min.css'),7);
module_config::register_css('theme','AdminLTE.css',full_link('/includes/plugin_theme_adminlte/css/AdminLTE.css'),12);

if(isset($_SERVER['REQUEST_URI']) && (strpos($_SERVER['REQUEST_URI'],_EXTERNAL_TUNNEL) || strpos($_SERVER['REQUEST_URI'],_EXTERNAL_TUNNEL_REWRITE))){
    module_config::register_css('theme','external.css',full_link('/includes/plugin_theme_adminlte/css/external.css'),100);
}


module_config::register_js('theme','jquery.min.js',full_link('/includes/plugin_theme_adminlte/js/jquery.min.js'),1);
module_config::register_js('theme','jquery-ui.min.js',full_link('/includes/plugin_theme_adminlte/js/jquery-ui-1.10.3.custom.min.js'),2);
module_config::register_js('theme','cookie.js',full_link('/js/cookie.js'),3);
module_config::register_js('theme','javascript.js',full_link('/js/javascript.js'),4);
module_config::register_js('theme','bootstrap.min.js',full_link('/includes/plugin_theme_adminlte/js/bootstrap.min.js'),6);
module_config::register_js('theme','app.js',full_link('/includes/plugin_theme_adminlte/js/AdminLTE/app.js'));
module_config::register_js('theme','adminlte.js',full_link('/includes/plugin_theme_adminlte/js/adminlte.js'));

function adminlte_dashboard_widgets() {
	$widgets = array();

	// the 4 column widget areas:
	foreach(glob(dirname(__FILE__).'/dashboard_widgets/widget_*.php') as $dashboard_widget_file){
		// echo $dashboard_widget_file;
		include($dashboard_widget_file);
	}

	return $widgets;
} // end hook function
hook_add( 'dashboard_widgets', 'adminlte_dashboard_widgets' );