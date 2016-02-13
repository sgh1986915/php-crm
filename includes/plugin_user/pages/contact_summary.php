<table width="100%" border="0" cellspacing="0" cellpadding="2" class="tableclass tableclass_form">
	<tbody>
		<tr>
			<th class="width1">
				<?php 
/** 
  * Copyright: dtbaker 2012
  * Licence: Please check CodeCanyon.net for licence details. 
  * More licence clarification available here:  http://codecanyon.net/wiki/support/legal-terms/licensing-terms/ 
  * Deploy: 9809 f200f46c2a19bb98d112f2d32a8de0c4
  * Envato: 4ffca17e-861e-4921-86c3-8931978c40ca
  * Package Date: 2015-11-25 02:55:20 
  * IP Address: 67.79.165.254
  */ echo _l('Contact Name'); ?>
			</th>
			<td>
				<?php echo $user_data['name'];?>
				<a href="<?php echo $this->link_open_contact($user_id);?>">&raquo;</a>
			</td>
		</tr>
		<tr>
			<th>
				<?php echo _l('Phone'); ?>
			</th>
			<td>
				<?php echo $user_data['phone'];?>
				&nbsp;&nbsp;<strong>Ext. <?php echo $user_data['phone_ext'];?></strong>
			</td>
		</tr>
		<tr>
			<th>
				<?php echo _l('Mobile'); ?>
			</th>
			<td>
				<?php echo $user_data['mobile'];?>
			</td>
		</tr>
		<tr>
			<th>
				<?php echo _l('Fax'); ?>
			</th>
			<td>
				<?php echo $user_data['fax'];?>
			</td>
		</tr>
		<tr>
			<th>
				<?php echo _l('Email'); ?>
			</th>
			<td>
				<a href="mailto:<?php echo $user_data['email'];?>"><?php echo $user_data['email'];?></a>
			</td>
		</tr>
	</tbody>
</table>