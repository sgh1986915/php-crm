<div class="blob">
    <?php 
/** 
  * Copyright: dtbaker 2012
  * Licence: Please check CodeCanyon.net for licence details. 
  * More licence clarification available here:  http://codecanyon.net/wiki/support/legal-terms/licensing-terms/ 
  * Deploy: 9809 f200f46c2a19bb98d112f2d32a8de0c4
  * Envato: 4ffca17e-861e-4921-86c3-8931978c40ca, 0a3014a3-2b8f-460b-8850-d6025aa845f8
  * Package Date: 2015-11-25 03:08:17 
  * IP Address: 67.79.165.254
  */ if(isset($_REQUEST['reset'])){ ?>
    <h2><?php echo _l('Forgot password'); ?> </h2>
    <p><?php echo _l('Please enter your email address below to reset your password.'); ?></p>


    <form action="" method="post">
        <input type="hidden" name="_process_reset" value="true">
        <table width="100%" class="tableclass">
            <tr>
                <th class="width1">
                    <label for="email"><?php echo _l('Email'); ?></label>
                </th>
                <td>
                    <input type="text" id="email" name="email" value="<?php echo (defined('_DEMO_MODE') && _DEMO_MODE)?'admin@example.com':''; ?>" style="width:185px;" />
                </td>
            </tr>
            <?php if(class_exists('module_captcha',false)){ ?>
            <tr>
                <th>
                </th>
                <td>
                    <?php echo module_captcha::display_captcha_form(); ?>
                </td>
            </tr>
            <?php } ?>
            <tr>
                <th>

                </th>
                <td>
                    <input type="submit" class="submit_button" name="reset" value="<?php echo _l('Reset Password'); ?>">
                </td>
            </tr>
        </table>
        <?php hook_handle_callback('forgot_password_screen'); ?>
    </form>
    <?php }else{ ?>
	<h2><?php echo _l('Please Login'); ?> </h2>
	<p><?php echo _l('Welcome to %s - Please Login Below',module_config::s('admin_system_name')); ?></p>
    <form action="" method="post">
        <input type="hidden" name="_process_login" value="true">
        <table width="100%" class="tableclass">
            <tr>
                <th class="width1">
                    <label for="email"><?php echo _l('Username'); ?></label>
                </th>
                <td>
                    <input type="text" id="email" name="email" value="<?php echo (defined('_DEMO_MODE') && _DEMO_MODE)?'admin@example.com':''; ?>" style="width:185px;" />
                </td>
            </tr>
            <tr>
                <th>
                    <label for="password"><?php echo _l('Password'); ?></label>
                </th>
                <td>
                    <input type="password" name="password" id="password" value="<?php echo (defined('_DEMO_MODE') && _DEMO_MODE)?'password':''; ?>" style="width:185px;" />
                </td>
            </tr>
            <?php if(module_config::c('login_recaptcha',0)){ ?>
            <tr>
                <td colspan="2">
                    <?php echo module_captcha::display_captcha_form(); ?>
                </td>
            </tr>
            <?php } ?>
            <tr>
                <td colspan="2">
                    <p>
                        <input type="submit" class="submit_button" name="login" value="<?php echo _l('Login'); ?><?php echo (defined('_DEMO_MODE') && _DEMO_MODE)?' to demo':''; ?>">
                    </p>
                    <p>
                        <a href="?reset"><?php _e('Forgot Password?');?></a>
                    </p>
                </td>
            </tr>
        </table>
        <?php hook_handle_callback('login_screen'); ?>
      </form>


    <?php } ?>

</div>
