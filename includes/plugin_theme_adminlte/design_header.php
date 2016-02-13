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

switch($display_mode){
    case 'ajax':

        break;
    case 'iframe':
    case 'normal':
    default:

    ?>
    <!DOCTYPE html>
    <html dir="<?php echo module_config::c('text_direction','ltr');?>"  id="html-<?php echo isset($page_unique_id) ? $page_unique_id : 'page';?>">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <title><?php echo $page_title; ?></title>

        <?php $header_favicon = module_theme::get_config('theme_favicon','');
            if($header_favicon){ ?>
                <link rel="icon" href="<?php echo htmlspecialchars($header_favicon);?>">
        <?php } ?>

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <?php module_config::print_css(_SCRIPT_VERSION);?>

        <?php module_config::print_js(_SCRIPT_VERSION);?>
        <!--
        Author: dtbaker.net
        Date: 2014-08-12
        Package: UCM
        -->
        <script type="text/javascript">
            $(function(){
                // override default button init - since we're using jquery ui here.
                ucm.init_buttons = function(){};
                ucm.init_interface();
                if(typeof ucm.adminlte != 'undefined'){
                    ucm.adminlte.init();
                }
                // calendar defaults
                <?php
                switch(strtolower(module_config::s('date_format','d/m/Y'))){
                    case 'd/m/y':
                        $js_cal_format = 'dd/mm/yy';
                        break;
                    case 'y/m/d':
                        $js_cal_format = 'yy/mm/dd';
                        break;
                    case 'm/d/y':
                        $js_cal_format = 'mm/dd/yy';
                        break;
                    default:
                        $js_cal_format = 'yy-mm-dd';
                }
                ?>
                $.datepicker.regional['ucmcal'] = {
                    closeText: '<?php echo addcslashes(_l('Done'),"'");?>',
                    prevText: '<?php echo addcslashes(_l('Prev'),"'");?>',
                    nextText: '<?php echo addcslashes(_l('Next'),"'");?>',
                    currentText: '<?php echo addcslashes(_l('Today'),"'");?>',
                    monthNames: ['<?php echo addcslashes(_l('January'),"'");?>','<?php echo addcslashes(_l('February'),"'");?>','<?php echo addcslashes(_l('March'),"'");?>','<?php echo addcslashes(_l('April'),"'");?>','<?php echo addcslashes(_l('May'),"'");?>','<?php echo addcslashes(_l('June'),"'");?>', '<?php echo addcslashes(_l('July'),"'");?>','<?php echo addcslashes(_l('August'),"'");?>','<?php echo addcslashes(_l('September'),"'");?>','<?php echo addcslashes(_l('October'),"'");?>','<?php echo addcslashes(_l('November'),"'");?>','<?php echo addcslashes(_l('December'),"'");?>'],
                    monthNamesShort: ['<?php echo addcslashes(_l('Jan'),"'");?>', '<?php echo addcslashes(_l('Feb'),"'");?>', '<?php echo addcslashes(_l('Mar'),"'");?>', '<?php echo addcslashes(_l('Apr'),"'");?>', '<?php echo addcslashes(_l('May'),"'");?>', '<?php echo addcslashes(_l('Jun'),"'");?>', '<?php echo addcslashes(_l('Jul'),"'");?>', '<?php echo addcslashes(_l('Aug'),"'");?>', '<?php echo addcslashes(_l('Sep'),"'");?>', '<?php echo addcslashes(_l('Oct'),"'");?>', '<?php echo addcslashes(_l('Nov'),"'");?>', '<?php echo addcslashes(_l('Dec'),"'");?>'],
                    dayNames: ['<?php echo addcslashes(_l('Sunday'),"'");?>', '<?php echo addcslashes(_l('Monday'),"'");?>', '<?php echo addcslashes(_l('Tuesday'),"'");?>', '<?php echo addcslashes(_l('Wednesday'),"'");?>', '<?php echo addcslashes(_l('Thursday'),"'");?>', '<?php echo addcslashes(_l('Friday'),"'");?>', '<?php echo addcslashes(_l('Saturday'),"'");?>'],
                    dayNamesShort: ['<?php echo addcslashes(_l('Sun'),"'");?>', '<?php echo addcslashes(_l('Mon'),"'");?>', '<?php echo addcslashes(_l('Tue'),"'");?>', '<?php echo addcslashes(_l('Wed'),"'");?>', '<?php echo addcslashes(_l('Thu'),"'");?>', '<?php echo addcslashes(_l('Fri'),"'");?>', '<?php echo addcslashes(_l('Sat'),"'");?>'],
                    dayNamesMin: ['<?php echo addcslashes(_l('Su'),"'");?>','<?php echo addcslashes(_l('Mo'),"'");?>','<?php echo addcslashes(_l('Tu'),"'");?>','<?php echo addcslashes(_l('We'),"'");?>','<?php echo addcslashes(_l('Th'),"'");?>','<?php echo addcslashes(_l('Fr'),"'");?>','<?php echo addcslashes(_l('Sa'),"'");?>'],
                    weekHeader: '<?php echo addcslashes(_l('Wk'),"'");?>',
                    dateFormat: '<?php echo $js_cal_format;?>',
                    firstDay: <?php echo module_config::c('calendar_first_day_of_week','1');?>,
                    yearRange: '<?php echo module_config::c('calendar_year_range','-90:+3');?>'
                };
                $.datepicker.setDefaults($.datepicker.regional['ucmcal']);

            });
        </script>
    </head>
    <?php if ( module_security::is_logged_in()) { ?>
    <body class="<?php echo module_theme::get_config( 'adminlte_colorstyle', 'dark' ) == 'light' ? 'skin-blue' : 'skin-black';?> <?php echo module_theme::get_config('adminlte_menustyle','fixed') == 'fixed' ? 'fixed' : '';?>"  id="<?php echo isset( $page_unique_id ) ? $page_unique_id : 'page'; ?>" <?php if ( $display_mode == 'iframe' ) {
	    echo ' style="background:#FFF;"';
    }?>>

	<?php if($display_mode == 'iframe'){ ?>

        <div id="iframe">

    <?php } else{
	    if ( _DEBUG_MODE ) {
		    module_debug::print_heading();
	    } ?>

	    <header class="header">
		    <a href="<?php echo _BASE_HREF; ?>" class="logo">
			    <!-- Add the class icon to your logo image or logo icon to add the margining -->
			    <?php if ( $header_logo = module_theme::get_config( 'theme_logo', _BASE_HREF . 'images/logo.png' ) ) { ?>
				    <img src="<?php echo htmlspecialchars( $header_logo ); ?>" border="0"
				         title="<?php echo htmlspecialchars( module_config::s( 'header_title', 'UCM' ) ); ?>">
			    <?php } else { ?>
				    <?php echo module_config::s( 'header_title', 'UCM' ); ?>
			    <?php } ?>
		    </a>
		    <!-- Header Navbar: style can be found in header.less -->
		    <nav class="navbar navbar-static-top" id="main-navbar" role="navigation">
			    <!-- Sidebar toggle button-->
			    <a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button">
				    <span class="sr-only">Toggle navigation</span>
				    <span class="icon-bar"></span>
				    <span class="icon-bar"></span>
				    <span class="icon-bar"></span>
			    </a>

			    <div class="navbar-right">

				    <?php if ( module_security::is_logged_in() ) { ?>
					    <ul class="nav navbar-nav">

						    <?php

						    $header_buttons = array();
						    if(module_security::is_logged_in()) {
							    $header_buttons = hook_filter_var( 'header_buttons', $header_buttons );
						    }
						    foreach($header_buttons as $header_button){
							    ?>
						        <li class="dropdown tasks-menu">
							        <a href="#" id="<?php echo $header_button['id'];?>">
									    <!-- <?php echo $header_button['title'];?> -->
								        <i class="fa fa-<?php echo $header_button['fa-icon'];?>"></i>
								    </a>
						        </li>
						    <?php
						    }

						    if ( class_exists( 'module_job', false ) && module_job::can_i( 'view', 'Jobs' ) ) {
							    if ( $job_todo_cache = module_cache::get( 'job', 'job_todo_header_cache' ) ) {
								    echo $job_todo_cache;
							    } else {
								    ob_start();
								    $this_alerts = array();
								    $todo_list   = module_job::get_tasks_todo();
								    $x           = 0;
								    if ( count( $todo_list ) > 0 ) {
									    ?>
									    <li class="dropdown tasks-menu">
										    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
											    <i class="fa fa-tasks"></i>
						                        <span
							                        class="label label-danger"><?php echo count( $todo_list ); ?></span>
										    </a>
										    <ul class="dropdown-menu">
											    <li class="header"><?php _e( 'You have %s tasks', count( $todo_list ) ); ?></li>
											    <li>
												    <ul class="menu">
													    <?php foreach ( $todo_list as $todo_item ) {
														    if ( $todo_item['hours_completed'] > 0 ) {
															    if ( $todo_item['hours'] > 0 ) {
																    $percentage = round( $todo_item['hours_completed'] / $todo_item['hours'], 2 );
																    $percentage = min( 1, $percentage );
															    } else {
																    $percentage = 1;
															    }
														    } else {
															    $percentage = 0;
														    }
														    $job_data = module_job::get_job( $todo_item['job_id'], false );
														    if ( $job_data && $job_data['job_id'] == $todo_item['job_id'] ) {
															    if ( $job_data['customer_id'] ) {
																    $customer_data = module_customer::get_customer( $job_data['customer_id'] );
																    if ( ! $customer_data || $customer_data['customer_id'] != $job_data['customer_id'] ) {
																	    continue;
																    }
															    } else {
																    $customer_data = array();
															    }
															    ?>
															    <li><!-- Task item -->
																    <a href="<?php echo module_job::link_open( $todo_item['job_id'], false, $job_data ); ?>">
																	    <h3>
																		    <?php
																		    echo isset( $customer_data['customer_name'] ) ? $customer_data['customer_name'] : '';
																		    echo ' <br/> ';
																		    echo $todo_item['description'];?>
																		    <small
																			    class="pull-right"><?php echo round( $percentage * 100 ); ?>
																			    %
																		    </small>
																	    </h3>
																	    <div class="progress xs">
																		    <div class="progress-bar progress-bar-aqua"
																		         style="width: <?php echo round( $percentage * 100 ); ?>%"
																		         role="progressbar"
																		         aria-valuenow="<?php echo round( $percentage * 100 ); ?>"
																		         aria-valuemin="0"
																		         aria-valuemax="100">
														                        <span
															                        class="sr-only"><?php _e( '%s%% Complete', round( $percentage * 100 ) ); ?></span>
																		    </div>
																	    </div>
																    </a>
															    </li><!-- end task item -->
														    <?php
														    }
													    } ?>
												    </ul>
											    </li>
											    <li class="footer">
												    <a href="<?php echo module_job::link_open( false ); ?>"><?php _e( 'View All Jobs' ); ?></a>
											    </li>
										    </ul>
									    </li>
								    <?php

								    }
								    $job_todo_cache = ob_get_clean();
								    echo $job_todo_cache;
								    module_cache::put( 'job', 'job_todo_header_cache', $job_todo_cache );
							    }
						    }

						    ?>

						    <!-- User Account: style can be found in dropdown.less -->
						    <li class="dropdown user user-menu">
							    <?php $user = module_user::get_user( module_security::get_loggedin_id() ); ?>
							    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
								    <i class="glyphicon glyphicon-user"></i>
								    <span><?php echo htmlspecialchars( $user['name'] ); ?> <i class="caret"></i></span>
							    </a>
							    <ul class="dropdown-menu">
								    <!-- User image -->
								    <li class="user-header bg-light-blue">
									    <?php if ( module_config::c( 'adminlte_enable_gravitar', 1 ) ) { ?>
										    <img
											    src="//www.gravatar.com/avatar/<?php echo md5( strtolower( trim( $user['email'] ) ) ); ?>"
											    class="img-circle" alt="User Image">
									    <?php } ?>
									    <p>
										    <a href="<?php echo module_user::link_open( module_security::get_loggedin_id() ); ?>">
											    <i class="fa fa-user"></i> <?php _e( 'Welcome %s', htmlspecialchars( $user['name'] ) ); ?>
										    </a>
									    </p>
									    <small><?php echo _l( '%s %s%s of %s %s', _l( date( 'D' ) ), date( 'j' ), _l( date( 'S' ) ), _l( date( 'F' ) ), date( 'Y' ) ); ?></small>
								    </li>
								    <!-- Menu Footer-->
								    <li class="user-footer">
									    <div class="pull-left">
										    <a href="<?php echo module_user::link_open( module_security::get_loggedin_id() ); ?>"
										       class="btn btn-default btn-flat"><?php _e( 'Profile' ); ?></a>
									    </div>
									    <div class="pull-right">
										    <a href="<?php echo _BASE_HREF; ?>index.php?_logout=true"
										       class="btn btn-default btn-flat"><?php _e( 'Sign out' ); ?></a>
									    </div>
								    </li>
							    </ul>
						    </li>
					    </ul>
				    <?php } ?>
			    </div>
		    </nav>
	    </header>


	    <div class="wrapper row-offcanvas row-offcanvas-left">
		    <!-- Left side column. contains the logo and sidebar -->
		    <aside class="left-side sidebar-offcanvas">
			    <!-- sidebar: style can be found in sidebar.less -->
			    <section class="sidebar">
				    <!-- Sidebar user panel -->
				    <div class="user-panel">
					    <?php $user = module_user::get_user( module_security::get_loggedin_id() );
					    if ( module_config::c( 'adminlte_enable_gravitar', 1 ) ) {
						    ?>
						    <div class="pull-left image" style="padding-top: 18px">
							    <img
								    src="//www.gravatar.com/avatar/<?php echo md5( strtolower( trim( $user['email'] ) ) ); ?>"
								    class="img-circle" alt="User Image">
						    </div>
					    <?php } ?>
					    <div class="pull-left info">
						    <p><?php _e( 'Welcome' );
							    echo '<br/>';
							    echo htmlspecialchars( $user['name'] ); ?></p>
						    <a href="<?php echo module_user::link_open( module_security::get_loggedin_id() ); ?>"><i
								    class="fa fa-user"></i> <?php _e( 'Edit Profile' ); ?></a> <br/>
						    <a href="#" onclick="return false;"><i
								    class="fa fa-calendar"></i> <?php echo _l( '%s %s%s of %s %s', _l( date( 'D' ) ), date( 'j' ), _l( date( 'S' ) ), _l( date( 'F' ) ), date( 'Y' ) ); ?>
						    </a>
					    </div>
				    </div>
				    <?php if ( module_security::can_user( module_security::get_loggedin_id(), 'Show Quick Search' ) ) {

					    if ( module_config::c( 'global_search_focus', 1 ) == 1 ) {
						    module_form::set_default_field( 'ajax_search_text' );
					    }
					    ?>
					    <!-- search form -->
					    <form action="<?php echo _BASE_HREF; ?>?p=search_results" method="post" class="sidebar-form">
						    <div class="input-group">
							    <input type="text" name="quick_search" class="form-control"
							           value="<?php echo isset( $_REQUEST['quick_search'] ) ? htmlspecialchars( $_REQUEST['quick_search'] ) : ''; ?>"
							           placeholder="<?php _e( 'Quick Search:' ); ?>"/>
	                                <span class="input-group-btn">
	                                    <button type='submit' name='seach' id='search-btn' class="btn btn-flat"><i
			                                    class="fa fa-search"></i></button>
	                                </span>
						    </div>
					    </form>
					    <!-- /.search form -->
				    <?php
				    } ?>


				    <!-- sidebar menu: : style can be found in sidebar.less -->
				    <?php
				    $menu_include_parent = false;
				    $show_quick_search   = true;
				    include( module_theme::include_ucm( "design_menu.php" ) );
				    ?>

			    </section>
			    <!-- /.sidebar -->
		    </aside>

	            <!-- Right side column. Contains the navbar and content of the page -->
		    <aside class="right-side">


		    <?php
		    // copied from print_header_message();
		    if ( ( isset( $_SESSION['_message'] ) && count( $_SESSION['_message'] ) ) || ( isset( $_SESSION['_errors'] ) && count( $_SESSION['_errors'] ) ) ) {
			    ?>
			    <div id="header_messages">
				    <?php if ( isset( $_SESSION['_message'] ) && count( $_SESSION['_message'] ) ) {
					    foreach ( $_SESSION['_message'] as $msg ) {
						    ?>
						    <div class="alert alert-success alert-dismissable" style="margin:20px 15px 10px 34px">
							    <i class="fa fa-check"></i>
							    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
							    <?php echo nl2br( ( $msg ) ); ?>
						    </div>

					    <?php
					    }
				    }
				    if ( isset( $_SESSION['_errors'] ) && count( $_SESSION['_errors'] ) ) {
					    foreach ( $_SESSION['_errors'] as $msg ) {
						    ?>
						    <div class="alert alert-danger alert-dismissable" style="margin:20px 15px 10px 34px">
							    <i class="fa fa-ban"></i>
							    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
							    <?php echo nl2br( ( $msg ) ); ?>
						    </div>

					    <?php
					    }
				    } ?>
			    </div>
			    <?php
			    $_SESSION['_message'] = array();
			    $_SESSION['_errors']  = array();
		    }
		    ?>
		    <!-- Content Header (Page header) -->
	                <section class="content-header">
		                <?php if (_DEMO_MODE){ ?>
		                <div class="alert alert-info alert-dismissable">
			                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
			                Welcome to the <strong>Ultimate Client Manager Demo</strong>. This is a preview of <a
				                href="http://codecanyon.net/item/ultimate-client-manager-crm-pro-edition/2621629?ref=dtbaker&utm_source=Demo&utm_medium=Header&utm_campaign=AdminLTE&utm_content=AdminLTE"
				                target="_blank">UCM Pro Edition</a> with the <a
				                href="http://themeforest.net/item/ucm-theme-adminlte-crm/8565409?ref=dtbaker&utm_source=Demo&utm_medium=Header&utm_campaign=AdminLTE&utm_content=AdminLTE"
				                target="_blank">AdminLTE theme</a> installed.  This demo resets every now and then, feel
			                free to test out all the features before purchase. (<a href="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']) . (strpos($_SERVER['REQUEST_URI'],'?') === false ? '?' : '&') . 'demo_theme=metis';?>">Swap back to default theme</a>)
		                </div>
		                <?php } ?>
		                <h1>
			                <?php
			                //<i class="fa fa-home"></i> {page title here}
			                echo isset( $GLOBALS['adminlte_main_title'] ) ? $GLOBALS['adminlte_main_title'] : $page_title;
			                ?>
		                </h1>
		                <!-- <ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
			<li><a href="#">Examples</a></li>
			<li class="active">Blank page</li>
		</ol> -->
	                </section>

	                <!-- Main content -->
		    <section class="content">


	    <?php } // iframe ?>

	    <div id="content">

	    <?php
	}else{
		// not logged in
	    ?>
        <body class="bg-black" id="login-page">
        <?php
	    print_header_message();
	}
} // switch

ob_start();