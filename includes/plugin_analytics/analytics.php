<?php
 class module_analytics extends module_base{
     public function init(){ }
     public function get_menu($holding_module=false,$holding_page=false,$type=false){
         $links=array();
         if(!$holding_module){
             // rendering the main menu:
             $links[]=array(
                  'm'=>'analytics',
                  'p'=>'google-analytics',
                 'name'=>'Google Analytics',
                 'order'=>999999,
             );
         }else if($holding_module == 'customer'){
             // rendering the customer menu
            
         }
         return $links;

     }
 }