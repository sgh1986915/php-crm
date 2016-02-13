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


hook_add('print_heading','adminlte_print_heading');
// copied from theme.php
function adminlte_print_heading($callback, $options){
    if(!is_array($options)){
        $options = array(
            'type' => 'h2',
            'title' => $options,
        );
    }
    $buttons = array();
    if(isset($options['button']) && is_array($options['button']) && count($options['button'])){
        $buttons = $options['button'];
        if(isset($buttons['url'])){
            $buttons = array($buttons);
        }
    }

    if(!isset($options['type'])){
        $options['type'] = 'h2';
    }
    if((isset($options['main']) && $options['main'])){ //} || !isset($GLOBALS['adminlte_main_title']) || !$GLOBALS['adminlte_main_title']){
        // save this one for use in the main header area of the theme
        ob_start();
        if(isset($options['icon_name'])&&$options['icon_name']){
            ?> <i class="fa fa-<?php echo $options['icon_name'];?>"></i> <?php
        }
        ?>
        <div class="header_buttons">
            <?php foreach($buttons as $button){ ?>
                <a href="<?php echo $button['url'];?>" class="btn btn-adminlte-5 btn-success btn-sm<?php echo isset($button['class'])?' '.$button['class']:'';?>"<?php if(isset($button['id'])) echo ' id="'.$button['id'].'"';?><?php if(isset($button['onclick'])) echo ' onclick="'.$button['onclick'].'"';?>>
                    <?php if(isset($button['type']) && $button['type'] == 'add'){ ?> <img src="<?php echo _BASE_HREF;?>images/add.png" width="10" height="10" alt="add" border="0" /> <?php } ?>
                    <span><?php echo _l($button['title']);?></span>
                </a>
            <?php } ?>
            <?php if(isset($options['help'])){ ?>
                <span class="button">
                    <?php _h($options['help']);?>
                </span>
            <?php } ?>
        </div>
        <?php if(get_display_mode() == 'iframe'){ ?>
		<h3 class="title">
            <?php echo isset($options['title_final']) ? $options['title_final'] : _l($options['title']);?>
        </h3>
        <?php }else{ ?>
        <span class="title">
            <?php echo isset($options['title_final']) ? $options['title_final'] : _l($options['title']);?>
        </span>
        <?php
        }
        $GLOBALS['adminlte_main_title'] = ob_get_clean();
        if(get_display_mode() == 'iframe'){
			echo $GLOBALS['adminlte_main_title'];
        }
    }else{
        ?>
        <<?php echo $options['type'];?> class="<?php echo isset($options['class']) ? $options['class'] : '';?>">
            <?php foreach($buttons as $button){ ?>
	            <span class="button">
	                <a href="<?php echo $button['url'];?>" class="btn btn-default btn-xs<?php echo isset($button['class'])?' '.$button['class']:'';?>"<?php if(isset($button['id'])) echo ' id="'.$button['id'].'"';?><?php if(isset($button['onclick'])) echo ' onclick="'.$button['onclick'].'"';?>>
	                    <?php if(isset($button['type']) && $button['type'] == 'add'){ ?> <img src="<?php echo _BASE_HREF;?>images/add.png" width="10" height="10" alt="add" border="0" /> <?php } ?>
	                    <span><?php echo _l($button['title']);?></span>
	                </a>
	            </span>
            <?php } ?>
            <?php if(isset($options['help'])){ ?>
                <span class="button">
                    <?php _h($options['help']);?>
                </span>
            <?php } ?>
            <?php if(isset($options['responsive']) && is_array($options['responsive']) && isset($options['responsive']['summary'])){ ?>
		        <span class="button responsive-toggle-button">
			        <a href="#" class="btn btn-default btn-xs no_permissions"><span class="responsive-hidden fa fa-plus-square"></span><span class="responsive-shown fa fa-minus-square"></span> </a>
		        </span>
            <?php } ?>
            <?php if(isset($options['responsive']) && is_array($options['responsive']) && isset($options['responsive']['title'])){ ?>
            <span class="title has-responsive">
                <span class="main-title"><?php echo isset($options['title_final']) ? $options['title_final'] : _l($options['title']);?></span>
			    <span class="responsive-title"> <?php _e( $options['responsive']['title']);?> </span>
            </span>
            <?php }else{ ?>
		    <span class="title">
                <?php echo isset($options['title_final']) ? $options['title_final'] : _l($options['title']);?>
            </span>
		    <?php } ?>
            <?php if(isset($options['responsive']) && is_array($options['responsive']) && isset($options['responsive']['summary'])){ ?>
		    <span class="responsive-summary"><?php echo $options['responsive']['summary'];?></span>
            <?php } ?>
        </<?php echo $options['type'];?>>
        <?php
    }
    return true;

}

hook_add('extra_fields_search_bar','adminlte_print_extra_search_bar');
function adminlte_print_extra_search_bar($callback, $owner_table,$options=array())
    {
        ob_start();
        // let the themes override this search bar function.
        if(module_extra::can_i('view','Extra Fields')){
            $defaults = module_extra::get_defaults($owner_table);
            $searchable_fields = array();
            foreach($defaults as $default){
                if(isset($default['searchable']) && $default['searchable']){
                    $searchable_fields[$default['key']] = $default;
                }
            }
            foreach($searchable_fields as $searchable_field){
                ?>
                <div class="form-group search_title">
                    <?php echo htmlspecialchars($searchable_field['key']);?>:
                </div>
                <div class="form-group search_input">
                    <?php
                    module_form::generate_form_element(array(
                        'type' => 'text',
                        'name' => 'search[extra_fields]['.htmlspecialchars($searchable_field['key']).']',
                        'class' => ' form-control input-sm',
                    ));?>
                </div>
                <?php
            }
        }
        return ob_get_clean();
    }

hook_add('search_bar','adminlte_search_bar');
// copied from form.php
function adminlte_search_bar($callback, $options){

    $defaults = array(
        'type' => 'table',
        'title' => _l('Filter By:'),
        'elements' => array(),
        'actions' => array(
            'search' => '<button type="submit" class="btn btn-default btn-sm">'. _l('Search') .'</button>', // create_link("Search","submit"),
        ),
    );
    $options = array_merge($defaults,$options);

    $id = 'filter-bar-'.md5(serialize($options));
    ob_start();
    ?>

    <nav class="search_bar navbar navbar-default" role="search">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#<?php echo $id;?>">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <?php if($options['title']){ ?>
                <a class="navbar-brand" href="#"><?php echo $options['title']; ?></a>
            <?php } ?>
          </div>
        <div class="collapse navbar-collapse" id="<?php echo $id;?>">
    <div class="navbar-form">
        <?php /*if($options['title']){ ?>
             <div class="form-group search_header"><strong><?php echo $options['title']; ?> </strong></div>
        <?php }*/ ?>
        <?php foreach($options['elements'] as $element){
                if(isset($element['field']) && !isset($element['fields'])){
                    $element['fields'] = array($element['field']);
                }
                if (isset($element['title']) && $element['title']){ ?>
                    <div class="form-group search_title">
                        <?php echo $element['title'];?>
                    </div>
                <?php } ?>
                    <div class="form-group search_input">
                <?php if (isset($element['fields'])){ ?>

                    <?php if(is_array($element['fields'])){
                        foreach($element['fields'] as $dataid => $field){
                            if(is_array($field)){
                                // treat this as a call to the form generate option
                                if(!isset($field['placeholder']) && isset($element['title']) && $element['title']){
                                    //$field['placeholder'] = $element['title'];
                                }
                                if(!isset($field['class']))$field['class'] = '';
                                $field['class'] .= ' form-control input-sm';
                                module_form::generate_form_element($field);
                                echo ' ';
                            }else if(is_closure($field)){
                                $field();
                            }else{
                                echo $field.' ';
                            }
                        }
                    }else{
                        echo $element['fields'];
                    }
                    ?>
                <?php
                }
                    ?> </div> <?php

            }
            if(class_exists('module_extra',false) && isset($options['extra_fields']) && $options['extra_fields']){
                // find out if any extra fields are searchable
                module_extra::print_search_bar($options['extra_fields']);
            }
            if($options['actions']){
                ?>
                <div class="form-group search_action pull-right">
                <?php
                foreach($options['actions'] as $action_id => $action){
                    echo $action .' ';
                }
                ?>
                </div>
                <?php
            }
            ?>
    </div>
    </div>
      </nav>

    <?php

    return ob_get_clean();
}

hook_add('extra_fields_output','adminlte_extra_fields_output');
function adminlte_extra_fields_output($callback,$html,$owner_table,$owner_id){
    // regex out the table rows if needed.
    if(module_theme::get_config('adminlte_formstyle','table') == 'div'){
        $html = str_replace('tbody','div',$html);
        if(preg_match_all('#<tr([^>]*)>(.*)</tr>#imsU',$html,$matches)){
            // convert these into <divs>
            /*<div class="form-group">
                    <div class="input-group">
                            <span class="input-group-addon"><span class="width1">Name</span></span>

            <input type="text" name="customer_name" value="1 New Theme Test" class="form-control  plugin_form_required">
                                            </div> <!-- /.input-group -->
            </div>*/
            foreach($matches[0] as $key => $val){
                preg_match('#<th[^>]*>(.*)</th>#imsU',$matches[2][$key],$title_match);
                preg_match('#<td[^>]*>(.*)</td>#imsU',$matches[2][$key],$body_match);
                $html = str_replace($val,'<div class="form-group extra-fields" '.$matches[1][$key].'><div class="input-group"><span class="input-group-addon"><span class="width1">'.$title_match[1].'</span></span>' .
                    '<div class="form-control">'.$body_match[1].'</div></div></div>'
                    ,$html);
            }
        }
    }else if(module_theme::get_config('adminlte_formstyle','table') == 'long'){
        $html = str_replace('tbody','div',$html);
        if(preg_match_all('#<tr([^>]*)>(.*)</tr>#imsU',$html,$matches)){
            // convert these into <divs>
            /*<div class="form-group">
                    <div class="input-group">
                            <span class="input-group-addon"><span class="width1">Name</span></span>

            <input type="text" name="customer_name" value="1 New Theme Test" class="form-control  plugin_form_required">
                                            </div> <!-- /.input-group -->
            </div>*/
            foreach($matches[0] as $key => $val){
                preg_match('#<th[^>]*>(.*)</th>#imsU',$matches[2][$key],$title_match);
                preg_match('#<td[^>]*>(.*)</td>#imsU',$matches[2][$key],$body_match);
                $html = str_replace($val,'<div class="form-group extra-fields" '.$matches[1][$key].'><label>'.$title_match[1].'</label>' .
                    '<div class="form-control">'.$body_match[1].'</div></div>'
                    ,$html);
            }
        }
    }
    return $html;
}
hook_add('generate_fieldset','adminlte_generate_fieldset');
// copied from form.php
function adminlte_generate_fieldset($callback,$options){
    $defaults = array(
	    'id' => false,
        'type' => 'table',
        'title' => false,
        'title_type' => 'h3',
        'heading' => false,
        'row_title_class' => 'width1',
        'row_data_class' => '',
        'elements' => array(),
        'class' => 'tableclass tableclass_form',
        'extra_settings' => array(),
        'elements_before' => '',
        'elements_after' => '',
    );
    $options = array_merge($defaults,$options);
	if(function_exists('hook_filter_var')){
		$options = hook_filter_var('generate_fieldset_options',$options);
	}
    ob_start();
     ?>

    <div class="box <?php echo module_theme::get_config('adminlte_boxstyle','box-solid'); echo isset($options['heading']['responsive']) ? ' box-responsive' : '';?>">
        <div class="box-header">
            <?php if($options['heading']){
                if(!isset($options['heading']['type']) || $options['heading']['type'] != 'h3'){
                    $options['heading']['type'] = 'h3';
                }
                $options['heading']['class'] = 'box-title';
                print_heading($options['heading']);
            }else if($options['title']){ ?>
                <<?php echo $options['title_type'];?> class="box-title"><?php _e($options['title']); ?></<?php echo $options['title_type'];?>>
            <?php } ?>
        </div>
        <!-- .block -->
        <div class="box-body">
            <?php echo $options['elements_before'];?>
            <?php if($options['elements']){
                if(module_theme::get_config('adminlte_formstyle','table') == 'table'){
                    ?>
                    <table class="<?php echo $options['class'];?>">
                        <tbody>
                        <?php
                        foreach($options['elements'] as $element){
                            if(isset($element['ignore']) && $element['ignore'])continue;
                            if(isset($element['field']) && !isset($element['fields'])){
                                $element['fields'] = array($element['field']);
                                unset($element['field']);
                            }
                            ?>
                            <tr>
                                <?php if((isset($element['message'])&&$element['message']) || (isset($element['warning'])&&isset($element['warning']))){ ?>
                                    <th colspan="2" class="text-center">
                                        <?php if(isset($element['message'])){ ?>
                                            <?php echo $element['message'];?>
                                        <?php }else if(isset($element['warning'])){ ?>
                                            <span class="error_text"><?php echo $element['warning'];?></span>
                                        <?php } ?>
                                    </th>
                                <?php }else{ ?>
                                    <th class="<?php echo isset($element['row_title_class']) ? $element['row_title_class'] : $options['row_title_class'];?>">
                                        <?php if(isset($element['title'])){ ?>
                                            <?php echo htmlspecialchars(_l($element['title']));?>
                                        <?php  } ?>
                                    </th>
                                    <td class="<?php echo isset($element['row_data_class']) ? $element['row_data_class'] : $options['row_data_class'];?>">
                                    <?php if(isset($element['fields'])){ ?>
                                        <?php if(is_array($element['fields'])){
                                            foreach($element['fields'] as $dataid => $field){
                                                if(is_array($field)){
                                                    // treat this as a call to the form generate option
                                                    module_form::generate_form_element($field);
                                                    echo ' ';
                                                }else if(is_closure($field)){
                                                    $field();
                                                }else{
                                                    echo $field.' ';
                                                }
                                            }
                                        }else{
                                            echo $element['fields'];
                                        }
                                        ?>
                                    <?php } // fields ?>
                                    </td> <!-- /.input-group -->
                                <?php
                                }//else
                                ?>
                            </tr> <!-- /.form-group -->
                        <?php
                        }
                        if(class_exists('module_extra') && module_extra::is_plugin_enabled() && $options['extra_settings']){
                            module_extra::display_extras($options['extra_settings']);
                        }
                        ?>
                        </tbody>
                        </table> <!-- /.elements -->
                    <?php
                    // end table layout
                }else if(module_theme::get_config('adminlte_formstyle','table') == 'div'){
                    ?>
                    <div class="<?php echo $options['class'];?>">
                        <?php
                        foreach($options['elements'] as $element){
                            if(isset($element['ignore']) && $element['ignore'])continue;
                            if(isset($element['field']) && !isset($element['fields'])){
                                $element['fields'] = array($element['field']);
                                unset($element['field']);
                            }
                            ?>
                            <div class="form-group">
                                <?php if((isset($element['message'])&&$element['message']) || (isset($element['warning'])&&isset($element['warning']))){ ?>
                                    <div class="text-center">
                                        <?php if(isset($element['message'])){ ?>
                                            <?php echo $element['message'];?>
                                        <?php }else if(isset($element['warning'])){ ?>
                                            <span class="error_text"><?php echo $element['warning'];?></span>
                                        <?php } ?>
                                    </div>
                                <?php }else{ ?>
                                    <div class="input-group<?php echo !isset($element['title']) ? '-notitle' : '';?>">
                                    <?php if(isset($element['title'])){ ?>
                                        <span class="input-group-addon table-row-title"><span class="<?php echo isset($element['row_title_class']) ? $element['row_title_class'] : $options['row_title_class'];?>"><?php echo htmlspecialchars(_l($element['title']));?></span></span>
                                    <?php  }
                                    if(isset($element['fields'])){ ?>

                                        <?php if(is_array($element['fields'])){
                                            // if there is only one element we put it up in the form-control so that it displays nicely.
                                            // if there are more than one elements we wrap them in a div form-control.
                                            $do_wrap = true;
                                            if(count($element['fields']) == 1){
                                                $field = current($element['fields']);
                                                if(is_array($field) && $field['type'] != 'wysiwyg' && $field['type'] != 'check' && $field['type'] != 'checkbox'){
                                                    $do_wrap = false;
	                                                $currency = false;
	                                                if($field['type'] == 'currency'){
		                                                $field['type'] = 'text';
		                                                $currency = true;
		                                                //$field['class'] = (isset($field['class']) ? $field['class'] : '') .' currency ';
	                                                }
                                                    $field['class'] = (isset($field['class']) ? $field['class'] : '') .' form-control ' . (isset($element['row_data_class']) ? $element['row_data_class'] : $options['row_data_class']);
		                                            $help_text = false;
		                                            if(isset($field['help'])){
			                                            // we put the help element outside in its own <span class="input-group-addon"></span>
			                                            // don't let the generatE_form_element produce it.
			                                            $help_text = $field['help'];
			                                            unset($field['help']);
		                                            }
                                                    module_form::generate_form_element($field);
	                                                if($currency){
		                                                ?>
		                                                <span class="input-group-addon"><?php echo currency('',true,isset($field['currency_id']) ? $field['currency_id'] : false);?></span>
		                                                <?php
	                                                }
		                                            if($help_text){
			                                            ?>
		                                                <span class="input-group-addon"><?php _h($help_text);?></span>
		                                                <?php
		                                            }
                                                }
                                            }
                                            if($do_wrap){
                                                ?> <div class="form-control<?php echo !isset($element['title']) ? '-notitle' : '';?> <?php echo isset($element['row_data_class']) ? $element['row_data_class'] : $options['row_data_class'];?>"> <?php
	                                            $help_text = false;
                                                foreach($element['fields'] as $dataid => $field) {
	                                                if ( is_array( $field ) && isset($field['help'])) {
		                                                // this element has a help text.
		                                                if($help_text){
			                                                // already a help text (shouldn't happen, ditch it.. and display multiple as normal)
			                                                $help_text = false;
			                                                break;
		                                                }else{
			                                                $help_text = $field['help'];
		                                                }
	                                                }
                                                }
                                                foreach($element['fields'] as $dataid => $field){
                                                    if(is_array($field)){
	                                                    if(isset($field['help']) && $help_text){
		                                                    unset($field['help']);
	                                                    }
                                                        // treat this as a call to the form generate option
                                                        module_form::generate_form_element($field);
                                                        echo ' ';
                                                    }else if(is_closure($field)){
                                                        $field();
                                                    }else{
                                                        echo $field.' ';
                                                    }
                                                }
                                                ?> </div> <?php
	                                            if($help_text){
		                                            ?>
	                                                <span class="input-group-addon"><?php _h($help_text);?></span>
	                                                <?php
	                                            }
                                            }

                                        }else{
                                            ?> <div class="form-control <?php echo isset($element['row_data_class']) ? $element['row_data_class'] : $options['row_data_class'];?>"> <?php
                                            echo $element['fields'];
                                            ?> </div> <?php
                                        }
                                        ?>
                                    <?php } // fields ?>
                                    </div> <!-- /.input-group -->
                                <?php
                                }//else
                                ?>
                            </div> <!-- /.form-group -->
                        <?php
                        }
                        if(class_exists('module_extra') && module_extra::is_plugin_enabled() && $options['extra_settings']){
                            module_extra::display_extras($options['extra_settings']);
                        }
                        ?>
                        </div> <!-- /.elements -->
                    <?php
                }else if(module_theme::get_config('adminlte_formstyle','table') == 'long'){
                    ?>
                    <div class="<?php echo $options['class'];?>">
                        <?php
                        foreach($options['elements'] as $element){
                            if(isset($element['ignore']) && $element['ignore'])continue;
                            if(isset($element['field']) && !isset($element['fields'])){
                                $element['fields'] = array($element['field']);
                                unset($element['field']);
                            }
                            ?>
                            <div class="form-group form-group-long">
                                <?php if((isset($element['message'])&&$element['message']) || (isset($element['warning'])&&isset($element['warning']))){ ?>
                                    <div class="text-center">
                                        <?php if(isset($element['message'])){ ?>
                                            <?php echo $element['message'];?>
                                        <?php }else if(isset($element['warning'])){ ?>
                                            <span class="error_text"><?php echo $element['warning'];?></span>
                                        <?php } ?>
                                    </div>
                                <?php }else{ ?>

                                    <?php if(isset($element['title'])){ ?>
                                        <label class="<?php echo isset($element['row_title_class']) ? $element['row_title_class'] : $options['row_title_class'];?>"><?php echo htmlspecialchars(_l($element['title']));?></label>
                                    <?php  }
                                    if(isset($element['fields'])){ ?>

                                        <?php if(is_array($element['fields'])){
                                            foreach($element['fields'] as $dataid => $field){
                                                if(is_array($field)){
                                                    // treat this as a call to the form generate option
	                                                $field['class'] = (isset($field['class']) ? $field['class'] : '').' '.(isset($element['row_data_class']) ? $element['row_data_class'] : $options['row_data_class']);
	                                                switch($field['type']){
		                                                case 'check':
		                                                case 'checkbox':
		                                                case 'wysiwyg':

			                                                break;
		                                                default:
			                                                 $field['class'] .= ' form-control ';
	                                                }
                                                    module_form::generate_form_element($field);
                                                    echo ' ';
                                                }else if(is_closure($field)){
                                                    $field();
                                                }else{
                                                    echo $field.' ';
                                                }
                                            }

                                        }else{
                                            ?> <div class="form-control <?php echo isset($element['row_data_class']) ? $element['row_data_class'] : $options['row_data_class'];?>"> <?php
                                            echo $element['fields'];
                                            ?> </div> <?php
                                        }
                                        ?>
                                    <?php } // fields ?>
                                <?php
                                }//else
                                ?>
                            </div> <!-- /.form-group -->
                        <?php
                        }
                        if(class_exists('module_extra') && module_extra::is_plugin_enabled() && $options['extra_settings']){
                            module_extra::display_extras($options['extra_settings']);
                        }
                        ?>
                        </div> <!-- /.elements -->
                    <?php
                }// end div layout
            }
            echo $options['elements_after'];?>
        <!-- /.block -->
        </div>
    </div>


    <?php

    return ob_get_clean();
}

hook_add('generate_form_actions','adminlte_generate_form_actions');
function adminlte_generate_form_actions($callback, $options){

        $defaults = array(
            'type' => 'action_bar',
            'class' => 'action_bar',
            'elements' => array(),
        );
        $options = array_merge($defaults,$options);
        //todo - hook in here for themes.
        ob_start();
        ?>
        <div class="box-footer">
        <div class="action_bar_duplicate <?php echo $options['class'];?>">
            <?php
            foreach($options['elements'] as $element){
                if(is_array($element) && !is_array(current($element))){
                    $element = array($element);
                }
                $element['fields'] = $element;
                ?>
                <span class="action">
                    <?php if(isset($element['fields'])){ ?>
                    <span class="action_element">
                        <?php if(is_array($element['fields'])){
                            foreach($element['fields'] as $dataid => $field){
                                if(is_array($field)){
                                    // treat this as a call to the form generate option
                                    switch($field['type']){
                                        case 'save_button':
                                            $field['type'] = 'submit';
                                            $field['class'] = (isset($field['class'])?$field['class'].' ':'') . 'submit_button btn btn-success';
                                            break;
                                        case 'submit':
                                            $field['type'] = 'submit';
                                            $field['class'] = (isset($field['class'])?$field['class'].' ':'') . 'submit_button btn btn-default';
                                            break;
                                        case 'delete_button':
                                            $field['type'] = 'submit';
                                            $field['class'] = (isset($field['class'])?$field['class'].' ':'') . 'submit_button btn btn-danger';
                                            break;
                                        case 'button':
                                            $field['type'] = 'button';
                                            $field['class'] = (isset($field['class'])?$field['class'].' ':'') . 'submit_button btn btn-default';
                                            break;
                                    }
                                    module_form::generate_form_element($field);
                                    echo ' ';
                                }else{
                                    echo $field.' ';
                                }
                            }
                        }else{
                            echo $element['fields'];
                        }
                        ?>
                    </span>
                <?php
                }
                ?>
                </span>
            <?php } ?>
        </div>
    </div>
        <?php

        return ob_get_clean();
    }

//hook_add('plugins_loaded','adminlte_plugins_loaded');
//function adminlte_plugins_loaded(){
    hook_remove('layout_column_half','module_theme::hook_handle_layout_column');
    hook_remove('layout_column_thirds','module_theme::hook_handle_layout_column');
    hook_add('layout_column_half','adminlte_layout_column');
    hook_add('layout_column_thirds','adminlte_layout_column');
//}

function adminlte_layout_column($column_type, $column_option = '', $column_width=false){
    switch($column_type){
        case 'layout_column_half':
            switch($column_option){
                case 1:
                    $column_width = $column_width ? floor(12 / (100/$column_width)) : 6;
                    echo '<div class="row"><div class="col-md-'.$column_width.'">';
                    break;
                case 2:
                    $column_width = $column_width ? ceil(12 / (100/$column_width)) : 6;
                    echo '</div><div class="col-md-'.$column_width.'">';
                    break;
                case 'end':
                    echo '</div></div>';
                    break;
            }
            break;
        case 'layout_column_thirds':
            if(!$column_width)$column_width=33;
            $column_width = $column_width ? round(12 / (100/$column_width)) : 4;
            switch($column_option){
                case 'start':
                    echo '<div class="row">';
                    break;
                case 'col_start':
                    echo '<div class="col-md-'.$column_width.'">';
                    break;
                case 'col_end':
                    echo '</div>';
                    break;
                case 'end':
                    echo '</div>';
                    break;
            }
            break;
    }
}