<div class="blob">
	<?php 
/** 
  * Copyright: dtbaker 2012
  * Licence: Please check CodeCanyon.net for licence details. 
  * More licence clarification available here:  http://codecanyon.net/wiki/support/legal-terms/licensing-terms/ 
  * Deploy: 9809 f200f46c2a19bb98d112f2d32a8de0c4
  * Envato: 4ffca17e-861e-4921-86c3-8931978c40ca
  * Package Date: 2015-11-25 02:55:20 
  * IP Address: 67.79.165.254
  */

    $step = (isset($_REQUEST['step'])) ? (int)$_REQUEST['step'] : 0;

    //print_heading('Setup Wizard (step '.$step.' of 4)');?>

    
    <p>
        <?php echo _l('Hello, Welcome to the setup wizard. You are currently on step %s of 5.',$step); ?>
    </p>

    <?php
    include('setup'.$step.'.php');
    ?>
      
</div>


