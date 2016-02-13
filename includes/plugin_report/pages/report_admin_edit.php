<?php 
/** 
  * Copyright: dtbaker 2012
  * Licence: Please check CodeCanyon.net for licence details. 
  * More licence clarification available here:  http://codecanyon.net/wiki/support/legal-terms/licensing-terms/ 
  * Deploy: 321 52e20b549dde146818983ece66d81a11
  * Envato: f2874e84-c8f9-4c6c-894f-2c79a77bf602
  * Package Date: 2012-05-29 04:20:08 
  * IP Address: 127.0.0.1
  */


$report_id = (int)$_REQUEST['report_id'];
$report = module_report::get_report($report_id);


if($report_id>0 && $report['report_id']==$report_id){
    $module->page_title = _l('Report: %s',$report['report_title']);
}else{
    $module->page_title = _l('Report: %s',_l('New'));
}

if($report_id>0 && $report){
	if(class_exists('module_security',false)){
		module_security::check_page(array(
            'module' => $module->module_name,
            'feature' => 'edit',
		));
	}
}else{
	if(class_exists('module_security',false)){
		module_security::check_page(array(
            'module' => $module->module_name,
            'feature' => 'create',
		));
	}
	module_security::sanatise_data('report',$report);
}



if($report_id>0 && isset($_REQUEST['o']) && $_REQUEST['o']=='xls'){
require_once 'php-excel.class.php';
// sending query
$sql=$report['notes'];

	$export = mysql_query($sql);
	if(mysql_errno()){
        set_error('SQL Error: '.mysql_error(). ' ' . $sql);
		?>
		<span class="button">
				<?php echo create_link("Edit","edit",module_report::link_generate($report['report_id'],array())); ?>
		</span><?
		return false;
	}
$fields_num = mysql_num_fields($export);

$xls = new Excel_XML('UTF-8', false,$report['report_title']);

$arr=array();
$namesArray=array();
       for($i=0; $i<$fields_num; $i++)
	   {
		$field = mysql_fetch_field($export);
		$namesArray[$i]= $field->name;
		}
		$arr[1]=$namesArray;
		  
		$i=2;
        while($row = mysql_fetch_array($export, MYSQL_NUM)) {
             $arr[$i]=$row;
			 $i+=1;
        }
$xls->addArray($arr);		
$data=$xls->generateXML();
$fileName=str_replace (' ','_',$report['report_title']);       
        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=$fileName.xls");
		header("Content-Transfer-Encoding: binary");
        header("Pragma: no-cache");
        header("Expires: 0");
        print "$data";
		exit;
}else if($report_id>0 && isset($_REQUEST['o']) && $_REQUEST['o']=='print'){	
	// sending query
	$sql=$report['notes'];

	$result = mysql_query($sql);
	if(mysql_errno()){
        set_error('SQL Error: '.mysql_error(). ' ' . $sql);
		?>
		<span class="button">
				<?php echo create_link("Edit","edit",module_report::link_generate($report['report_id'],array())); ?>
		</span><?
		return false;
	}

	$fields_num = mysql_num_fields($result);

	echo "<h2>".$report['report_title']."</h2>";
	?>
	
	<table class="search_bar" width="100%">
	<tr>
	<td>
       <span class="button">
			<?php echo create_link("Edit","edit",module_report::link_generate($report['report_id'],array())); ?>
			</span>
			<span class="button">
				<?php echo create_link("Xls","xls",module_report::link_generate($report['report_id'],array('arguments'=>array('o'=>'xls')))); ?>
			</span> 
	</td>		
	</tr>
	</table>
	
	
			
	
	<table width="100%" border="0" cellspacing="0" cellpadding="2" class="tableclass tableclass_rows">

	<tr class="title">
	<?

	// printing table headers
	for($i=0; $i<$fields_num; $i++)
	{
		$field = mysql_fetch_field($result);
		echo "<th>"._l($field->name)."</th>";
	}
	echo "</tr>\n";
	$c=0;
	// printing table rows
	while($row = mysql_fetch_row($result))
	{
		?>
		<tr class="<?php echo ($c++%2)?"odd":"even"; ?>">
		<?

		// $row is array... foreach( .. ) puts every element
		// of $row to $cell variable
		foreach($row as $cell)
			echo "<td>$cell</td>";

		echo "</tr>\n";
	}?>
	
	</table><?
	mysql_free_result($result);
return false;
}
?>

<form action="" method="post">
	<input type="hidden" name="_process" value="save_report" />
    <input type="hidden" name="report_id" value="<?php echo $report_id; ?>" />
    

    <?php

    $fields = array(
    'fields' => array(
        'report_title' => 'report_title',
    ));
    module_form::set_required(
        $fields
    );
    module_form::prevent_exit(array(
        'valid_exits' => array(
            // selectors for the valid ways to exit this form.
            '.submit_button',
        ))
    );
    

    ?>

	<table cellpadding="10" width="100%">
		<tbody>
			<tr>
				<td valign="top" width="35%">
					<h3><?php echo _l(_l('Report').' Details'); ?></h3>



					<table border="0" cellspacing="0" cellpadding="2" class="tableclass tableclass_form tableclass_full">
						<tbody>
							<tr>
								<th class="width1">
									<?php echo _l('Name'); ?>
								</th>
								<td>
									<input type="text" name="report_title" id="report_title" value="<?php echo htmlspecialchars($report['report_title']); ?>" />
								</td>
							</tr>
                            <?php if(1){ ?>
							<tr>
								<th>
									<?php echo _l('Sql'); ?>
								</th>
								<td>
									<textarea  name="notes" id="notes" cols="40" rows="5"><?php echo htmlspecialchars($report['notes']); ?></textarea>
								</td>
							</tr>
                            <?php } ?>
							
						</tbody>
                        
					</table>

				</td>
               
			</tr>
			<tr>
				<td align="center" colspan="2">
					<input type="submit" name="butt_save" id="butt_save" value="<?php echo _l('Save '._l('Report')); ?>" class="submit_button save_button" />
					<?php if((int)$report_id && module_report::can_i('delete','reports')){ ?>
					<input type="submit" name="butt_del" id="butt_del" value="<?php echo _l('Delete'); ?>" class="submit_button delete_button" />
					<?php } ?>
					<input type="button" name="cancel" value="<?php echo _l('Cancel'); ?>" onclick="window.location.href='<?php echo module_report::link_open(false); ?>';" class="submit_button" />
				</td>
			</tr>
			<tr>
				<td align="center" colspan="3">
					<span class="button">
					<?php echo create_link("Print","print",module_report::link_generate($report['report_id'],array('arguments'=>array('o'=>'print')))); ?>
				</span>
				<span class="button">
					<?php echo create_link("Xls","xls",module_report::link_generate($report['report_id'],array('arguments'=>array('o'=>'xls')))); ?>
				</span> 
				</td>
			</tr>
		</tbody>
	</table>


</form>
