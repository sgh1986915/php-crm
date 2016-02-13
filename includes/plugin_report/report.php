<?php 
/** 
  * Copyright: dudum 2012
  * Licence: Please check CodeCanyon.net for licence details. 
  * More licence clarification available here:  http://codecanyon.net/wiki/support/legal-terms/licensing-terms/ 
*/



class module_report extends module_base{
	
	public $links;
	public $report_types;

    public static function can_i($actions,$name=false,$category=false,$module=false){
        if(!$module)$module=__CLASS__;
        return parent::can_i($actions,$name,$category,$module);
    }
	public static function get_class() {
        return __CLASS__;
    }
	public function init(){
		$this->links = array();
		$this->report_types = array();
		$this->module_name = "report";
		$this->module_position = 18;
        $this->version = 2.21;
    

        if($this->can_i('view',_l('Reports'))){
            $this->ajax_search_keys = array(
                _DB_PREFIX.'report' => array(
                    'plugin' => 'report',
                    'search_fields' => array(
                        'report_title'
                    ),
                    'key' => 'report_id',
                    'title' => _l(_l('Report').': '),
                     'icon_name' => 'line-chart',
                ),
            );

            $this->links[] = array(
                "name"=>_l('Reports'),
                "p"=>"report_admin",
                'args'=>array('report_id'=>false),
                 'icon_name' => 'line-chart',
            );

        }
		
	}

    public static function link_generate($report_id=false,$options=array(),$link_options=array()){

        $key = 'report_id';
        if($report_id === false && $link_options){
            foreach($link_options as $link_option){
                if(isset($link_option['data']) && isset($link_option['data'][$key])){
                    ${$key} = $link_option['data'][$key];
                    break;
                }
            }
            if(!${$key} && isset($_REQUEST[$key])){
                ${$key} = $_REQUEST[$key];
            }
        }
        $bubble_to_module = false;
        if(!isset($options['type']))$options['type']='report';
        $options['page'] = 'report_admin';
        if(!isset($options['arguments'])){
            $options['arguments'] = array();
        }
        $options['arguments']['report_id'] = $report_id;
        $options['module'] = 'report';
        if((int)$report_id > 0){
            $data = self::get_report($report_id);
            $options['data'] = $data;
        }else{
            $data = array();
            if(!isset($options['full']) || !$options['full']){
                // we are not doing a full <a href> link, only the url (eg: create new report)
            }else{
                // we are trying to do a full <a href> link -
                return _l('N/A');
            }
        }
        // what text should we display in this link?
        $options['text'] = (!isset($data['report_id'])||!trim($data['report_id'])) ? _l('N/A') : $data['report_id'];
       
        array_unshift($link_options,$options);

        if(!module_security::has_feature_access(array(
            'name' => 'Customers',
            'module' => 'customer',
            'category' => 'Customer',
            'view' => 1,
            'description' => 'view',
        ))){
            if(!isset($options['full']) || !$options['full']){
                return '#';
            }else{
                return isset($options['text']) ? $options['text'] : _l('N/A');
            }

        }
        if($bubble_to_module){
            global $plugins;
            return $plugins[$bubble_to_module['module']]->link_generate(false,array(),$link_options);
        }else{
            // return the link as-is, no more bubbling or anything.
            // pass this off to the global link_generate() function
            return link_generate($link_options);

        }
    }

	public static function link_open($report_id,$full=false){
        return self::link_generate($report_id,array('full'=>$full));
    }

	
	public function process(){
		$errors=array();
		if(isset($_REQUEST['butt_del']) && $_REQUEST['butt_del'] && $_REQUEST['report_id']){
            $data = self::get_report($_REQUEST['report_id']);
            if(module_form::confirm_delete('report_id',"Really delete "._l('Report').": ".$data['name'],self::link_open($_REQUEST['report_id']))){
                $this->delete_report($_REQUEST['report_id']);
                set_message(_l('Report')." deleted successfully");
                redirect_browser(self::link_open(false));
            }
		}else if("save_report" == $_REQUEST['_process']){
			$report_id = $this->save_report($_REQUEST['report_id'],$_POST);
			$_REQUEST['_redirect'] = $this->link_open($report_id);
			set_message(_l('Report')." saved successfully");
		}
		if(!count($errors)){
			redirect_browser($_REQUEST['_redirect']);
			exit;
		}
		print_error($errors,true);
	}


	public static function get_reports($search=array()){
		// limit based on customer id
		/*if(!isset($_REQUEST['customer_id']) || !(int)$_REQUEST['customer_id']){
			return array();
		}*/
		// build up a custom search sql query based on the provided search fields
		$sql = "SELECT u.*,u.report_id AS id ";
        $sql .= ", u.report_title AS name ";
        // add in our extra fields for the csv export
        //if(isset($_REQUEST['import_export_go']) && $_REQUEST['import_export_go'] == 'yes'){
        if(class_exists('module_extra',false)){
            $sql .= " , (SELECT GROUP_CONCAT(ex.`extra_key` ORDER BY ex.`extra_id` ASC SEPARATOR '"._EXTRA_FIELD_DELIM."') FROM `"._DB_PREFIX."extra` ex WHERE owner_id = u.report_id AND owner_table = 'report') AS extra_keys";
            $sql .= " , (SELECT GROUP_CONCAT(ex.`extra` ORDER BY ex.`extra_id` ASC SEPARATOR '"._EXTRA_FIELD_DELIM."') FROM `"._DB_PREFIX."extra` ex WHERE owner_id = u.report_id AND owner_table = 'report') AS extra_vals";
        }
        $from = " FROM `"._DB_PREFIX."report` u ";
		$where = " WHERE 1 ";
		if(isset($search['generic']) && $search['generic']){
			$str = mysql_real_escape_string($search['generic']);
			$where .= " AND ( ";
			$where .= " u.report_title LIKE '%$str%' ";
			$where .= ' ) ';
		}

		$group_order = ' GROUP BY u.report_id ORDER BY u.report_title'; // stop when multiple company sites have same region
		$sql = $sql . $from . $where . $group_order;
		$result = qa($sql);
		//module_security::filter_data_set("report",$result);
		return $result;
//		return get_multiple("report",$search,"report_id","fuzzy","name");

	}
	public static function get_report($report_id){
		$report = get_single("report","report_id",$report_id);

        if(!$report){
            $report = array(
                'report_id' => 'new',
                'report_title' => '',
                'notes' => '',
            );
        }
		return $report;
	}
	public function save_report($report_id,$data){
        if((int)$report_id>0){
            $original_report_data = $this->get_report($report_id);
            if(!$original_report_data || $original_report_data['report_id'] != $report_id){
                $original_report_data = array();
                $report_id = false;
            }
        }else{
            $original_report_data = array();
            $report_id = false;
        }

        // check create permissions.
        if(!$report_id && !self::can_i('create','reports')){
            // user not allowed to create reports.
            set_error('Unable to create new reports');
            redirect_browser(self::link_open(false));
        }
		
		$report_id = update_insert("report_id",$report_id,"report",$data);
       
        module_extra::save_extras('report','report_id',$report_id);
		return $report_id;
	}

	public static function delete_report($report_id){
		$report_id=(int)$report_id;
		if(_DEMO_MODE && $report_id == 1){
			return;
		}
        if((int)$report_id>0){
            $original_report_data = self::get_report($report_id);
            if(!$original_report_data || $original_report_data['report_id'] != $report_id){
                return false;
            }
        }
        if(!self::can_i('delete','reports')){
            return false;
        }
		$sql = "DELETE FROM "._DB_PREFIX."report WHERE report_id = '".$report_id."' LIMIT 1";
		$res = query($sql);
       
		module_note::note_delete("report",$report_id);
        module_extra::delete_extras('report','report_id',$report_id);
	}
    public function login_link($report_id){
        return module_security::generate_auto_login_link($report_id);
    }

    public function get_install_sql(){
        ob_start();
        ?>
CREATE TABLE `<?php echo _DB_PREFIX; ?>report` (
  `report_id` int(11) NOT NULL auto_increment,
  `date_created` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `report_title` varchar(255) NOT NULL,
  `notes` text NOT NULL,
  PRIMARY KEY  (`report_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    <?php
        
        return ob_get_clean();
    }

    public static function handle_import($data,$add_to_group){

    }

}