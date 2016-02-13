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

if(module_security::is_logged_in()) {
	switch ( $display_mode ) {
		case 'iframe':
			?>
			</div> <!-- /#content -->
			</section><!-- /.content -->
			</aside><!-- /.right-side -->
			</div><!-- ./wrapper -->

			</body>
			</html>
			<?php
			module_debug::push_to_parent();
			break;
		case 'ajax':

			break;
		case 'normal':
		default:
			/*
			<div id="footer">
				<p>&copy; <?php echo module_config::s('admin_system_name','Ultimate Client Manager'); ?>
				  - <?php echo date("Y"); ?>
				  - <?php _e('Version:');?> <?php echo module_config::current_version(); ?>
				  - <?php _e('Time:');?> <?php echo round(microtime(true)-$start_time,5);?>
				</p>
			</div>
	*/
			?>


				</div> <!-- /#content -->
				</section><!-- /.content -->
				</aside><!-- /.right-side -->
				</div><!-- ./wrapper -->


				</body>
				</html>
			<?php
			break;
	}
}else{
	?>
	</body></html>
	<?php
}