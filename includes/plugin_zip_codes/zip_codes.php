<?php
 class module_zip_codes extends module_base{
     public function init(){ }
     public function get_menu($holding_module=false,$holding_page=false,$type=false){
         $links=array();
         if(!$holding_module){
             // rendering the main menu:
             $links[]=array(
                  'm'=>'zip_codes',
                  'p'=>'example',
                 'name'=>'Zip Codes',
                 'order'=>999999,
                'icon_name' => 'search-plus',
             );
         }else if($holding_module == 'customer'){
             // rendering the customer menu
             
         }
         return $links;

     }
 }