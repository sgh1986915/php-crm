<?php 
/** 
  * Copyright: dtbaker 2012
  * Licence: Please check CodeCanyon.net for licence details. 
  * More licence clarification available here:  http://codecanyon.net/wiki/support/legal-terms/licensing-terms/ 
  * Deploy: 9809 f200f46c2a19bb98d112f2d32a8de0c4
  * Envato: 4ffca17e-861e-4921-86c3-8931978c40ca, 0a3014a3-2b8f-460b-8850-d6025aa845f8
  * Package Date: 2015-11-25 03:08:17 
  * IP Address: 67.79.165.254
  */

$cron_start_time = time();

chdir(dirname(__FILE__)); //

require_once('includes/config.php');

if(!defined('_UCM_SECRET')){
    define('_UCM_SECRET',_UCM_FOLDER);
}

// if we are runing this from a web browser then we have to have a correct hash.
if(isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST']){
    // check hash.
    $correct_hash = md5(_UCM_SECRET.' secret hash ');
    if(!isset($_REQUEST['hash']) || $_REQUEST['hash'] != $correct_hash){
        echo 'failed - please check cron.php link in settings';
        exit;
    }
}

$_SERVER['REMOTE_ADDR'] = false;
$_SERVER['HTTP_HOST'] = false;
$_SERVER['REQUEST_URI'] = false;

$noredirect = true;
$disable_sessions = true;
require_once("init.php");

// stop running cron multiple times
$cron_minimum_delay = 180; // 180 seconds = 3 mins.
$last_cron_run_time = module_config::c('cron_last_run',0);
if($last_cron_run_time>0 && $last_cron_run_time + $cron_minimum_delay >= $cron_start_time){
    // the last cron job ran less than 3 minutes ago, don't run it again.
    exit;
}


$cron_debug = module_config::c('debug_cron_jobs',0);

foreach($plugins as $plugin_name => &$plugin){
    if(method_exists($plugin,'run_cron')){
        if($cron_debug){
            echo "Running $plugin_name cron job <br>\n";
        }
        $plugin -> run_cron($cron_debug);
    }
}


module_config::save_config('cron_last_run',$cron_start_time);
