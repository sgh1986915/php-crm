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

$search = (isset($_REQUEST['search']) && is_array($_REQUEST['search'])) ? $_REQUEST['search'] : array();

$reports = module_report::get_reports($search);


// hack to add a "group" option to the pagination results.
if(class_exists('module_group',false)){
    module_group::enable_pagination_hook(
        // what fields do we pass to the group module from this customers?
        array(
            'fields'=>array(
                'owner_id' => 'report_id',
                'owner_table' => 'report',
                'name' => 'name',
                'email' => ''
            ),
        )
    );
}
if(class_exists('module_table_sort',false)){
    module_table_sort::enable_pagination_hook(
    // pass in the sortable options.
        array(
            'table_id' => 'report_list',
            'sortable'=>array(
                // these are the "ID" values of the <th> in our table.
                // we use jquery to add the up/down arrows after page loads.
                'report_name' => array(
                    'field' => 'name',
                    'current' => 1, // 1 asc, 2 desc
                ),/*
                'report_url' => array(
                    'field' => 'url',
                ),
                'report_customer' => array(
                    'field' => 'customer_name',
                ),
                'report_status' => array(
                    'field' => 'status',
                ),*/
                // special case for group sorting.
                'report_group' => array(
                    'group_sort' => true,
                    'owner_table' => 'report',
                    'owner_id' => 'report_id',
                ),
            ),
        )
    );
}
// hack to add a "export" option to the pagination results.
if(class_exists('module_import_export',false) && module_report::can_i('view','Export '._l('Reports'))){
    module_import_export::enable_pagination_hook(
        // what fields do we pass to the import_export module from this customers?
        array(
            'name' => _l('Report').' Export',
            'fields'=>array(
                _l('Report').' ID' => 'report_id',
                'Report Title' => 'report_title',
                'Sql' => 'notes',
            ),
            // do we look for extra fields?
            'extra' => array(
                'owner_table' => 'report',
                'owner_id' => 'report_id',
            ),
        )
    );
}

?>

<h2>
    <?php if(module_report::can_i('create','reports')){ ?>
	<span class="button">
		<?php echo create_link("Add New "._l('Report'),"add",module_report::link_open('new')); ?>
	</span>
    <?php } ?>
    <?php if(class_exists('module_import_export',false) && module_report::can_i('view','Import '._l('Reports'))){
        $link = module_import_export::import_link(
            array(
                'callback'=>'module_report::handle_import',
                'name'=>_l('Reports'),
                'return_url'=>$_SERVER['REQUEST_URI'],
                'group'=>'report',
                'fields'=>array(
                    _l('Report').' ID' => 'report_id',
                    'Report Title' => 'report_title',
                    'Sql' => 'notes'
                ),
                // do we attempt to import extra fields?
                'extra' => array(
                    'owner_table' => 'report',
                    'owner_id' => 'report_id',
                ),
            )
        );
        ?>
      <!--  <span class="button">
            <?php echo create_link("Import "._l('Reports'),"add",$link); ?>
        </span> -->
        <?php
    } ?>
	<?php echo _l('Reports'); ?>
</h2>

<form action="" method="post">


<table class="search_bar" width="100%">
	<tr>
        <th width="70"><?php _e('Filter By:'); ?></th>
        <td width="40">
            <?php _e('Title:');?>
        </td>
        <td>
            <input type="text" name="search[generic]" value="<?php echo isset($search['generic'])?htmlspecialchars($search['generic']):''; ?>" size="30">
        </td>
		<td width="30">
        </td>
        <td>
       </td>
        <td align="right">
			<?php echo create_link("Reset","reset",module_report::link_open(false)); ?>
			<?php echo create_link("Search","submit"); ?>
		</td>
	</tr>
</table>

<?php
$pagination = process_pagination($reports);
$colspan = 4;
?>

<?php echo $pagination['summary'];?>

<table width="100%" border="0" cellspacing="0" cellpadding="2" class="tableclass tableclass_rows">
	<thead>
	<tr class="title">
		<th id="report_id"><?php echo _l('Id'); ?></th>
        <?php if(1){ ?>
		<th id="report_title"><?php echo _l('Title'); ?></th>
        <?php } ?>
        <th id="report_note"><?php echo _l('Sql'); ?></th>
		<th id="report_lupdate"><?php echo _l('Operations'); ?></th>
    </tr>
    </thead>
    <tbody>
		<?php
		$c=0;
		foreach($pagination['rows'] as $report){
            ?>
		<tr class="<?php echo ($c++%2)?"odd":"even"; ?>">
			<td class="row_action">
				<?php echo module_report::link_open($report['report_id'],true);?>
			 </td>
             <td>
                <?php echo htmlspecialchars($report['report_title']);?>
            </td>
            <td>
                <?php echo htmlspecialchars($report['notes']);?>
            </td>
            <td>
			<span class="button">
				<?php echo create_link("Edit","edit",module_report::link_generate($report['report_id'],array())); ?>
			</span>
			<span class="button">
				<?php echo create_link("Print","print",module_report::link_generate($report['report_id'],array('arguments'=>array('o'=>'print')))); ?>
			</span>
			<span class="button">
				<?php echo create_link("Xls","xls",module_report::link_generate($report['report_id'],array('arguments'=>array('o'=>'xls')))); ?>
			</span> 
            </td>
           
		</tr>
		<?php } ?>
	</tbody>
</table>
    <?php echo $pagination['links'];?>
</form>