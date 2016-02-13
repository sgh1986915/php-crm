<?php
/*
 *  Module: webNpro Menu Editor v1.0
 *  Copyright: Kőrösi Zoltán | webNpro - hosting and design | http://webnpro.com | info@webnpro.com
 *  Contact / Feature request: info@webnpro.com (Hungarian / English)
 *  License: Please check CodeCanyon.net for license details.
 *  More license clarification available here:  http://codecanyon.net/wiki/support/legal-terms/licensing-terms/
 */

// Get the infos about the plugin from the plugin.info file
$info = parse_ini_file(dirname(__FILE__) . '/../plugin.info');
$plugin_full_name = $info['fullname'];
$plugin_name = $info['modulename'];
$plugin_id = $info['id'];
$plugin_ver = $info['version'];

// Check permissions
if (!module_webnpro_menu_editor::can_i('edit', 'webNpro Menu Editor Settings', 'Config') && (module_security::get_loggedin_id() != '1')) {
    redirect_browser(_BASE_HREF);
}

if (isset($_POST['cancel_edit'])) {
    header('Location: ' . $_SERVER['REQUEST_URI']);
} elseif (isset($_POST['reset_menu_items'])) {
    module_webnpro_menu_editor::reset_menu();
} elseif (isset($_POST['save_menu_items'])) {
    module_webnpro_menu_editor::save_menu($_POST);
}


// Header buttons
$header_buttons[] = array(
    'url' => _BASE_HREF . '?m[0]=' . $plugin_name . '&p[0]=documentation',
    'title' => _l('Read Documentation')
);

// Print the heading with the header buttons
print_heading(array(
    'type' => 'h2',
    'main' => true,
    'title' => _l($plugin_full_name) . ' v' . $plugin_ver,
    'button' => $header_buttons,
));


$new_menu_row = '<tr class="tbl_row">';
$new_menu_row .= '<td><input type="hidden" name="menu_position[]" placeholder="menu position"></td>';
$new_menu_row .= '<td><span class="fa fa-arrows">&nbsp;</span><span class="fa fa-unlock">&nbsp;</span></td>';
$new_menu_row .= '<td><input  class="form-control" type="text" name="menu_name[]" placeholder="menu name"></td>';
//$new_menu_row .= '<td><input type="text" name="menu_icon[]" placeholder="menu icon"></td>';
$new_menu_row .= '<td><select class="form-control" name="menu_icon[]">' . module_webnpro_menu_editor::get_select_FontAwesomeIcons() . '</select></td>';
$new_menu_row .= '<td><input  class="form-control" type="text" name="menu_url[]" placeholder="menu url"></td>';
$new_menu_row .= '<td><input  class="form-control" type="hidden" name="menu_module[]" placeholder="menu module"></td>';
$new_menu_row .= '<td><input  class="form-control" type="hidden" name="menu_page[]" placeholder="menu page"></td>';

$new_menu_row .= '<td>&nbsp;<a href="#" class="btn btn-success add_field">+</a></td>';
$new_menu_row .= '<td><a href="#" class="btn btn-danger remove_field">-</a></td>';
$new_menu_row .= '</tr>';

$menu_items = module_webnpro_menu_editor::get_menu_items();

echo '<form action="" method="POST">';
echo '<table id="menu_items" class="input_fields_wrap">';
echo '<tbody>';
echo '<tr style="text-align: center; font-weight: bold; border-bottom: 1px #ccc solid; margin-bottom: 5px;"><td></td><td></td><td>Menu text</td><td>Icon</td><td>URL</td><td>Module</td><td>Module Page</td><td></td><td></td></tr>';
if (count($menu_items)) {
    foreach ($menu_items as $menu_item) {
        $readonly = ((($menu_item['p'] != '') || ($menu_item['m'] == 'dashboard')) ? 'readonly' : '');
        echo '<tr class="tbl_row">';
        echo '<td><input type="hidden" name="menu_position[]" placeholder="menu position" value="' . $menu_item['order'] . '"></td>';
        echo '<td><span class="fa fa-arrows">&nbsp;</span><span class="fa fa-' . (($readonly != '') ? 'lock' : 'unlock') . '">&nbsp;</span></td>';
        echo '<td><input ' . $readonly . '  class="form-control" type="text" name="menu_name[]" placeholder="menu name" value="' . preg_replace("/ <span.*/", "", $menu_item['name']) . '"></td>';
        //echo '<td><input ' . $readonly . '  class="form-control" type="text" name="menu_icon[]" placeholder="menu icon" value="' . $menu_item['icon_name'] . '"></td>';
        if ($readonly != '') {
            echo '<td><select disabled class="form-control" name="menu_icon[]">' . module_webnpro_menu_editor::get_select_FontAwesomeIcons($menu_item['icon_name']) . '</select>'
            . '<input type="hidden" name="menu_icon[]" value="' . $menu_item['icon_name'] . '"></td>';
        } else {
            echo '<td><select class="form-control" name="menu_icon[]">' . module_webnpro_menu_editor::get_select_FontAwesomeIcons($menu_item['icon_name']) . '</select></td>';
        }
        echo '<td><input ' . $readonly . '  class="form-control" type="' . (($menu_item['p'] != '') ? 'hidden' : 'text') . '" name="menu_url[]" placeholder="menu url" value="' . $menu_item['url'] . '"></td>';
        echo '<td><input ' . $readonly . '  class="form-control" type="' . (($menu_item['p'] == '') ? 'hidden' : 'text') . '" name="menu_module[]" placeholder="module name" value="' . $menu_item['m'] . '"></td>';
        echo '<td><input ' . $readonly . '  class="form-control" type="' . (($menu_item['p'] == '') ? 'hidden' : 'text') . '" name="menu_page[]" placeholder="menu page" value="' . $menu_item['p'] . '"></td>';
        echo '<td>&nbsp;<a href="#" class="btn btn-success add_field">+</a></td>';
        if (!$readonly) {
            echo '<td><a href="#" class="btn btn-danger remove_field">-</a></td>';
        } else {
            echo '<td><i class="fa fa-lock"></i></td>';
        }
        echo '</tr>';
    }
} else {
    echo $new_menu_row;
}
echo '</tbody></table><br/>';
echo '<button class="btn btn-success add_field_button">Add More Fields</button>&nbsp;&nbsp;&nbsp;&nbsp;<input class="btn btn-success" type="submit" name="save_menu_items" value="Save menus">&nbsp&nbsp;&nbsp;&nbsp;<input class="btn btn-default" type="submit" name="cancel_edit" value="Cancel">'
 . '&nbsp;&nbsp;&nbsp;&nbsp;<input class="btn btn-danger" type="submit" name="reset_menu_items" value="RESET MENUS | REMOVE ALL CUSTOM MENU SETTINGS">';
echo '</form>';
echo '<div style="clear: both;"></div><br/><div><hr/>';
echo module_webnpro_menu_editor::get_select_FontAwesomeIcons('', true);
echo '</div>';
?>
<script type="text/javascript">
    $(document).ready(function () {

        // Return a helper with preserved width of cells
        var fixHelper = function (e, ui) {
            ui.children().each(function () {
                $(this).width($(this).width());
            });
            return ui;
        };

        $("#menu_items tbody").sortable({
            helper: fixHelper
        }).disableSelection();

        var wrapper = $(".input_fields_wrap"); //Fields wrapper
        var add_button = $(".add_field_button"); //Add button ID

        $(add_button).click(function (e) { //on add input button click
            e.preventDefault();
            $(wrapper).append('<?php echo $new_menu_row; ?>'); //add input box
        });

        $(wrapper).on("click", ".add_field", function (e) { //user click on remove text
            e.preventDefault();
            $(this).closest('tr').after('<?php echo $new_menu_row; ?>'); //add input box
        });

        $(wrapper).on("click", ".remove_field", function (e) { //user click on remove text
            e.preventDefault();
            $(this).closest('tr').remove();
        })
    });


</script>