<?php

/*
 *  Module: webNpro Menu Editor v1.0
 *  Copyright: Kőrösi Zoltán | webNpro - hosting and design | http://webnpro.com | info@webnpro.com
 *  Contact / Feature request: info@webnpro.com (Hungarian / English)
 *  License: Please check CodeCanyon.net for license details.
 *  More licence clarification available here:  http://codecanyon.net/wiki/support/legal-terms/licensing-terms/
 *
 *  Changelog:
 *
 *  v1.1    -   02/19/2015  -   BUGFIX: Font Awesome Firefox bug
 *  v1.0    -   02/13/2015  -   First release
 *                              Feature: Add custom links to the main menu
 *                              Feature: Assign FontAwesome icon to the custom menu items
 *                              Feature: Add custom links before the Dashboard link
 *                              Feature: Re-order menu items (except the Dashboard link)
 *                              Feature: Reset the menu (remove all customizations)
 *                              Developer info: The plugin makes and modify UCM module in the includes folder for every custom menu item
 *                              WARNING: This plugin is working ONLY with the AdminLTE theme!!!
 *
 */

/**
 * webNpro Menu Editor module class
 */
class module_webnpro_menu_editor extends module_base {

    public $links;

    /**
     * Standard UCM function for permissions checking
     *
     * @param string $actions
     * @param string $name
     * @param string $category
     * @param string $module
     * @return boolean
     */
    public static function can_i($actions, $name = false, $category = false, $module = false) {
        if (!$module)
            $module = __CLASS__;
        return parent::can_i($actions, $name, $category, $module);
        /* END public static function can_i($actions, $name = false, $category = false, $module = false) */
    }

    /**
     * Standard UCM function with the base module datas
     */
    public function init() {
        $this->links = array();
        $this->module_name = "webnpro_menu_editor";
        $this->module_position = 99999;
        $this->version = '1.1';

        // Include the css files
        module_config::register_css('webnpro_menu_editor', 'webnpro_menu_editor.css');
        module_config::register_css('webnpro_menu_editor','font-awesome.css',full_link('/includes/plugin_webnpro_menu_editor/css/font-awesome.css'),100);
		//module_config::register_css('webnpro_menu_editor', 'font-awesome.css');

        /* END public function init() */
    }

    /**
     * Standard UCM function to generate the menu items
     *
     * @global $load_modules
     */
    public function pre_menu() {
        global $load_modules;

        // Menu => Settings / Menu editor
        if ($this->can_i('edit', 'webNpro Menu Editor Settings', 'Config')) {
            $this->links[] = array(
                "name" => "Menu Editor",
                "p" => "menu_editor",
                'holder_module' => 'config',
                'holder_module_page' => 'config_admin',
                'menu_include_parent' => 0,
            );
        }

        /* END public function pre_menu() */
    }

    /**
     * Function to sort the menu links by orders
     * @param type $a
     * @param type $b
     * @return int
     */
    private static function sort_menu_links($a, $b) {
        if (isset($a['order']) && isset($b['order'])) {
            return $a['order'] > $b['order'];
        }
        return 1;

        /* END private static function sort_menu_links($a, $b) */
    }

    /**
     * Function to get the menu items from the installed modules
     * @global array $plugins
     * @return array
     */
    public function get_menu_items() {
        global $plugins;
        if (!isset($menu_items)) {
            $menu_items = array();
        }

        // We have to add the Dashboard link to the array
        $menu_items[] = array(
            "name" => "Dashboard",
            "url" => _BASE_HREF . 'index.php?p=home',
            "icon_name" => 'home',
            "m" => 'dashboard',
            "order" => module_config::c('_menu_order_dashboard', 0)
        );

        $current_module_name = (isset($module)) ? $module->module_name : false;
        foreach ($plugins as $plugin_name => &$plugin) {
            $menu_item = $plugin->get_menu($current_module_name, 'main');
            $menu_items = array_merge($menu_items, $menu_item);
        }
        // Remove duplicates
        $s_menu = array();
        foreach ($menu_items as $menu_item) {
            $s_menu[] = serialize($menu_item);
        };
        $u_menu = array_unique($s_menu);
        $menu_items = array();
        foreach ($u_menu as $u_item) {
            $menu_items[] = unserialize($u_item);
        }
        // Sort by order
        uasort($menu_items, array('module_webnpro_menu_editor', 'sort_menu_links'));
        return $menu_items;

        /* END public function get_menu_items() */
    }

    /**
     * Recursive folder delete
     * @param string $dir
     * @return boolean
     */
    public function delTree($dir) {
        if ($dir == '') {
            return true;
        }
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file") && !is_link($dir)) ? delTree("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);

        /* END public function delTree($dir) */
    }

    /**
     * Remove the custom menu module folders from the includes folder
     */
    public function remove_menu_plugins() {
        $menu_module_folders = glob(_UCM_FOLDER . "includes/plugin_webnpro_menu_module_*");
        foreach ($menu_module_folders as $menu_module_folder) {
            module_webnpro_menu_editor::delTree($menu_module_folder);
        }

        /* END public function remove_menu_plugins() */
    }

    /**
     * Create the custom menu module folders in the includes folder
     * @param array $menu_items
     */
    public function create_menu_plugins($menu_items) {
        // Remove all custom menu module folders
        module_webnpro_menu_editor::remove_menu_plugins();
        // Create the new custom menu module folders
        foreach ($menu_items as $menu_item) {
            $menu_item_name = module_webnpro_menu_editor::slug($menu_item['name']);
            $menu_plugin_name = _UCM_FOLDER . "includes/plugin_webnpro_menu_module_" . $menu_item_name . "/webnpro_menu_module_" . $menu_item_name . ".php";
            mkdir(dirname($menu_plugin_name), 0755, true);
            $myfile = fopen($menu_plugin_name, "w") or die("Unable to open file!");
            $content = file_get_contents(dirname(__FILE__) . '/templates/menu_plugin.php');
            $from = array('|MENU_CLASS_NAME|', '|MENU_NAME|', '|MENU_ICON|', '|MENU_URL|', '|MENU_POSITION|');
            $to = array($menu_item_name, $menu_item['name'], $menu_item['icon_name'], $menu_item['url'], $menu_item['order']);
            $content = str_replace($from, $to, $content);
            fwrite($myfile, $content);
            fclose($myfile);
        }

        /* END public function create_menu_plugins($menu_items) */
    }

    /**
     * Reset the menu (remove all customizations)
     */
    public function reset_menu() {
        module_webnpro_menu_editor::remove_menu_plugins();
        $sql = "DELETE FROM `" . _DB_PREFIX . "config` WHERE `key` LIKE  '%_menu_order%'";
        query($sql);
        header('Location: ' . $_SERVER['REQUEST_URI']);

        /* END public function reset_menu() */
    }

    /**
     * Save menu
     * @param array $items
     */
    public function save_menu($items) {
        if (!isset($menu_items)) {
            $menu_items = array();
        }

        if (!isset($external_menu_items)) {
            $external_menu_items = array();
        }

        if (!isset($items)) {
            $items = array();
        }

        $before_dashboard = true;
        $i = 0;
        foreach ($items['menu_name'] as $item) {
            if ($items['menu_name'][$i] != '') {
                $menu_items[$i]['name'] = $items['menu_name'][$i];
                $menu_items[$i]['icon_name'] = $items['menu_icon'][$i];
                $menu_items[$i]['url'] = $items['menu_url'][$i];
                $menu_items[$i]['m'] = ($items['menu_module'][$i] != '') ? $items['menu_module'][$i] : "webnpro_menu_module_" . module_webnpro_menu_editor::slug($menu_item['name']);
                $menu_items[$i]['p'] = $items['menu_page'][$i];
                if ($menu_items[$i]['m'] == 'dashboard') {
                    $before_dashboard = false;
                }
                if ($before_dashboard) {
                    $menu_items[$i]['order'] = $i - 9999;
                } else {
                    $menu_items[$i]['order'] = $i;
                }
                if (($menu_items[$i]['url'] != '') && ($menu_items[$i]['m'] != 'dashboard')) {
                    $external_menu_items[] = $menu_items[$i];
                }
                // Save menu order
                module_config::save_config('_menu_order_' . $menu_items[$i]['m'], $i);
                $i++;
            }
        }
        // Create new custom menu modules
        if (count($external_menu_items)) {
            module_webnpro_menu_editor::create_menu_plugins($external_menu_items);
            header('Location: ' . $_SERVER['REQUEST_URI']);
        }

        /* END public function save_menu($items) */
    }

    /**
     * Remove accents from the string
     * @param string $str
     * @return string
     */
    private function remove_accent($str) {
        $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ő', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ű', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ő', 'ö', 'ø', 'ù', 'ú', 'û', 'ű', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ');
        $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
        return str_replace($a, $b, $str);

        /* END private function remove_accent($str) */
    }

    /**
     * Create slug from the string
     * @param string $str
     * @return string
     */
    private function slug($str) {
        return strtolower(preg_replace(array('/[^a-zA-Z0-9 -]/', '/[ -]+/', '/^-|-$/'), array('', '_', ''), module_webnpro_menu_editor::remove_accent($str)));

        /* END private function slug($str) */
    }

    /**
     * If $help is false it gives back select options with the icon names
     * If $help is true it gives back the icons with names
     * @param string $selected_icon
     * @param boolean $help
     * @return string
     */
    public function get_select_FontAwesomeIcons($selected_icon, $help = false) {
        $fa_icons = array(
            array('class' => 'glass', 'name' => 'Glass'),
            array('class' => 'music', 'name' => 'Music'),
            array('class' => 'search', 'name' => 'Search'),
            array('class' => 'envelope-o', 'name' => 'Envelope Outlined'),
            array('class' => 'heart', 'name' => 'Heart'),
            array('class' => 'star', 'name' => 'Star'),
            array('class' => 'star-o', 'name' => 'Star Outlined'),
            array('class' => 'user', 'name' => 'User'),
            array('class' => 'film', 'name' => 'Film'),
            array('class' => 'th-large', 'name' => 'Th Large'),
            array('class' => 'th', 'name' => 'Th'),
            array('class' => 'th-list', 'name' => 'Th List'),
            array('class' => 'check', 'name' => 'Check'),
            array('class' => 'times', 'name' => 'Times'),
            array('class' => 'search-plus', 'name' => 'Search Plus'),
            array('class' => 'search-minus', 'name' => 'Search Minus'),
            array('class' => 'power-off', 'name' => 'Power Off'),
            array('class' => 'signal', 'name' => 'Signal'),
            array('class' => 'cog', 'name' => 'Cog'),
            array('class' => 'trash-o', 'name' => 'Trash Outlined'),
            array('class' => 'home', 'name' => 'Home'),
            array('class' => 'file-o', 'name' => 'File Outlined'),
            array('class' => 'clock-o', 'name' => 'Clock Outlined'),
            array('class' => 'road', 'name' => 'Road'),
            array('class' => 'download', 'name' => 'Download'),
            array('class' => 'arrow-circle-o-down', 'name' => 'Arrow Circle Outlined (Down)'),
            array('class' => 'arrow-circle-o-up', 'name' => 'Arrow Circle Outlined (Up)'),
            array('class' => 'inbox', 'name' => 'Inbox'),
            array('class' => 'play-circle-o', 'name' => 'Play Circle Outlined'),
            array('class' => 'repeat', 'name' => 'Repeat'),
            array('class' => 'refresh', 'name' => 'Refresh'),
            array('class' => 'list-alt', 'name' => 'List Alt'),
            array('class' => 'lock', 'name' => 'Lock'),
            array('class' => 'flag', 'name' => 'Flag'),
            array('class' => 'headphones', 'name' => 'Headphones'),
            array('class' => 'volume-off', 'name' => 'Volume Off'),
            array('class' => 'volume-down', 'name' => 'Volume (Down)'),
            array('class' => 'volume-up', 'name' => 'Volume (Up)'),
            array('class' => 'qrcode', 'name' => 'Qrcode'),
            array('class' => 'barcode', 'name' => 'Barcode'),
            array('class' => 'tag', 'name' => 'Tag'),
            array('class' => 'tags', 'name' => 'Tags'),
            array('class' => 'book', 'name' => 'Book'),
            array('class' => 'bookmark', 'name' => 'Bookmark'),
            array('class' => 'print', 'name' => 'Print'),
            array('class' => 'camera', 'name' => 'Camera'),
            array('class' => 'font', 'name' => 'Font'),
            array('class' => 'bold', 'name' => 'Bold'),
            array('class' => 'italic', 'name' => 'Italic'),
            array('class' => 'text-height', 'name' => 'Text Height'),
            array('class' => 'text-width', 'name' => 'Text Width'),
            array('class' => 'align-left', 'name' => 'Align (Left)'),
            array('class' => 'align-center', 'name' => 'Align Center'),
            array('class' => 'align-right', 'name' => 'Align (Right)'),
            array('class' => 'align-justify', 'name' => 'Align Justify'),
            array('class' => 'list', 'name' => 'List'),
            array('class' => 'outdent', 'name' => 'Outdent'),
            array('class' => 'indent', 'name' => 'Indent'),
            array('class' => 'video-camera', 'name' => 'Video Camera'),
            array('class' => 'picture-o', 'name' => 'Picture Outlined'),
            array('class' => 'pencil', 'name' => 'Pencil'),
            array('class' => 'map-marker', 'name' => 'Map Marker'),
            array('class' => 'adjust', 'name' => 'Adjust'),
            array('class' => 'tint', 'name' => 'Tint'),
            array('class' => 'pencil-square-o', 'name' => 'Pencil Square Outlined'),
            array('class' => 'share-square-o', 'name' => 'Share Square Outlined'),
            array('class' => 'check-square-o', 'name' => 'Check Square Outlined'),
            array('class' => 'arrows', 'name' => 'Arrows'),
            array('class' => 'step-backward', 'name' => 'Step Backward'),
            array('class' => 'fast-backward', 'name' => 'Fast Backward'),
            array('class' => 'backward', 'name' => 'Backward'),
            array('class' => 'play', 'name' => 'Play'),
            array('class' => 'pause', 'name' => 'Pause'),
            array('class' => 'stop', 'name' => 'Stop'),
            array('class' => 'forward', 'name' => 'Forward'),
            array('class' => 'fast-forward', 'name' => 'Fast Forward'),
            array('class' => 'step-forward', 'name' => 'Step Forward'),
            array('class' => 'eject', 'name' => 'Eject'),
            array('class' => 'chevron-left', 'name' => 'Chevron (Left)'),
            array('class' => 'chevron-right', 'name' => 'Chevron (Right)'),
            array('class' => 'plus-circle', 'name' => 'Plus Circle'),
            array('class' => 'minus-circle', 'name' => 'Minus Circle'),
            array('class' => 'times-circle', 'name' => 'Times Circle'),
            array('class' => 'check-circle', 'name' => 'Check Circle'),
            array('class' => 'question-circle', 'name' => 'Question Circle'),
            array('class' => 'info-circle', 'name' => 'Info Circle'),
            array('class' => 'crosshairs', 'name' => 'Crosshairs'),
            array('class' => 'times-circle-o', 'name' => 'Times Circle Outlined'),
            array('class' => 'check-circle-o', 'name' => 'Check Circle Outlined'),
            array('class' => 'ban', 'name' => 'Ban'),
            array('class' => 'arrow-left', 'name' => 'Arrow (Left)'),
            array('class' => 'arrow-right', 'name' => 'Arrow (Right)'),
            array('class' => 'arrow-up', 'name' => 'Arrow (Up)'),
            array('class' => 'arrow-down', 'name' => 'Arrow (Down)'),
            array('class' => 'share', 'name' => 'Share'),
            array('class' => 'expand', 'name' => 'Expand'),
            array('class' => 'compress', 'name' => 'Compress'),
            array('class' => 'plus', 'name' => 'Plus'),
            array('class' => 'minus', 'name' => 'Minus'),
            array('class' => 'asterisk', 'name' => 'Asterisk'),
            array('class' => 'exclamation-circle', 'name' => 'Exclamation Circle'),
            array('class' => 'gift', 'name' => 'Gift'),
            array('class' => 'leaf', 'name' => 'Leaf'),
            array('class' => 'fire', 'name' => 'Fire'),
            array('class' => 'eye', 'name' => 'Eye'),
            array('class' => 'eye-slash', 'name' => 'Eye Slash'),
            array('class' => 'exclamation-triangle', 'name' => 'Exclamation Triangle'),
            array('class' => 'plane', 'name' => 'Plane'),
            array('class' => 'calendar', 'name' => 'Calendar'),
            array('class' => 'random', 'name' => 'Random'),
            array('class' => 'comment', 'name' => 'Comment'),
            array('class' => 'magnet', 'name' => 'Magnet'),
            array('class' => 'chevron-up', 'name' => 'Chevron (Up)'),
            array('class' => 'chevron-down', 'name' => 'Chevron (Down)'),
            array('class' => 'retweet', 'name' => 'Retweet'),
            array('class' => 'shopping-cart', 'name' => 'Shopping Cart'),
            array('class' => 'folder', 'name' => 'Folder'),
            array('class' => 'folder-open', 'name' => 'Folder Open'),
            array('class' => 'arrows-v', 'name' => 'Arrows V'),
            array('class' => 'arrows-h', 'name' => 'Arrows H'),
            array('class' => 'bar-chart', 'name' => 'Bar Chart'),
            array('class' => 'twitter-square', 'name' => 'Twitter Square'),
            array('class' => 'facebook-square', 'name' => 'Facebook Square'),
            array('class' => 'camera-retro', 'name' => 'Camera Retro'),
            array('class' => 'key', 'name' => 'Key'),
            array('class' => 'cogs', 'name' => 'Cogs'),
            array('class' => 'comments', 'name' => 'Comments'),
            array('class' => 'thumbs-o-up', 'name' => 'Thumbs Outlined (Up)'),
            array('class' => 'thumbs-o-down', 'name' => 'Thumbs Outlined (Down)'),
            array('class' => 'star-half', 'name' => 'Star Half'),
            array('class' => 'heart-o', 'name' => 'Heart Outlined'),
            array('class' => 'sign-out', 'name' => 'Sign Out'),
            array('class' => 'linkedin-square', 'name' => 'Linkedin Square'),
            array('class' => 'thumb-tack', 'name' => 'Thumb Tack'),
            array('class' => 'external-link', 'name' => 'External Link'),
            array('class' => 'sign-in', 'name' => 'Sign In'),
            array('class' => 'trophy', 'name' => 'Trophy'),
            array('class' => 'github-square', 'name' => 'Github Square'),
            array('class' => 'upload', 'name' => 'Upload'),
            array('class' => 'lemon-o', 'name' => 'Lemon Outlined'),
            array('class' => 'phone', 'name' => 'Phone'),
            array('class' => 'square-o', 'name' => 'Square Outlined'),
            array('class' => 'bookmark-o', 'name' => 'Bookmark Outlined'),
            array('class' => 'phone-square', 'name' => 'Phone Square'),
            array('class' => 'twitter', 'name' => 'Twitter'),
            array('class' => 'facebook', 'name' => 'Facebook'),
            array('class' => 'github', 'name' => 'Github'),
            array('class' => 'unlock', 'name' => 'Unlock'),
            array('class' => 'credit-card', 'name' => 'Credit Card'),
            array('class' => 'rss', 'name' => 'Rss'),
            array('class' => 'hdd-o', 'name' => 'Hdd Outlined'),
            array('class' => 'bullhorn', 'name' => 'Bullhorn'),
            array('class' => 'bell', 'name' => 'Bell'),
            array('class' => 'certificate', 'name' => 'Certificate'),
            array('class' => 'hand-o-right', 'name' => 'Hand Outlined (Right)'),
            array('class' => 'hand-o-left', 'name' => 'Hand Outlined (Left)'),
            array('class' => 'hand-o-up', 'name' => 'Hand Outlined (Up)'),
            array('class' => 'hand-o-down', 'name' => 'Hand Outlined (Down)'),
            array('class' => 'arrow-circle-left', 'name' => 'Arrow Circle (Left)'),
            array('class' => 'arrow-circle-right', 'name' => 'Arrow Circle (Right)'),
            array('class' => 'arrow-circle-up', 'name' => 'Arrow Circle (Up)'),
            array('class' => 'arrow-circle-down', 'name' => 'Arrow Circle (Down)'),
            array('class' => 'globe', 'name' => 'Globe'),
            array('class' => 'wrench', 'name' => 'Wrench'),
            array('class' => 'tasks', 'name' => 'Tasks'),
            array('class' => 'filter', 'name' => 'Filter'),
            array('class' => 'briefcase', 'name' => 'Briefcase'),
            array('class' => 'arrows-alt', 'name' => 'Arrows Alt'),
            array('class' => 'users', 'name' => 'Users'),
            array('class' => 'link', 'name' => 'Link'),
            array('class' => 'cloud', 'name' => 'Cloud'),
            array('class' => 'flask', 'name' => 'Flask'),
            array('class' => 'scissors', 'name' => 'Scissors'),
            array('class' => 'files-o', 'name' => 'Files Outlined'),
            array('class' => 'paperclip', 'name' => 'Paperclip'),
            array('class' => 'floppy-o', 'name' => 'Floppy Outlined'),
            array('class' => 'square', 'name' => 'Square'),
            array('class' => 'bars', 'name' => 'Bars'),
            array('class' => 'list-ul', 'name' => 'List Ul'),
            array('class' => 'list-ol', 'name' => 'List Ol'),
            array('class' => 'strikethrough', 'name' => 'Strikethrough'),
            array('class' => 'underline', 'name' => 'Underline'),
            array('class' => 'table', 'name' => 'Table'),
            array('class' => 'magic', 'name' => 'Magic'),
            array('class' => 'truck', 'name' => 'Truck'),
            array('class' => 'pinterest', 'name' => 'Pinterest'),
            array('class' => 'pinterest-square', 'name' => 'Pinterest Square'),
            array('class' => 'google-plus-square', 'name' => 'Google Plus Square'),
            array('class' => 'google-plus', 'name' => 'Google Plus'),
            array('class' => 'money', 'name' => 'Money'),
            array('class' => 'caret-down', 'name' => 'Caret (Down)'),
            array('class' => 'caret-up', 'name' => 'Caret (Up)'),
            array('class' => 'caret-left', 'name' => 'Caret (Left)'),
            array('class' => 'caret-right', 'name' => 'Caret (Right)'),
            array('class' => 'columns', 'name' => 'Columns'),
            array('class' => 'sort', 'name' => 'Sort'),
            array('class' => 'sort-desc', 'name' => 'Sort Desc'),
            array('class' => 'sort-asc', 'name' => 'Sort Asc'),
            array('class' => 'envelope', 'name' => 'Envelope'),
            array('class' => 'linkedin', 'name' => 'Linkedin'),
            array('class' => 'undo', 'name' => 'Undo'),
            array('class' => 'gavel', 'name' => 'Gavel'),
            array('class' => 'tachometer', 'name' => 'Tachometer'),
            array('class' => 'comment-o', 'name' => 'Comment Outlined'),
            array('class' => 'comments-o', 'name' => 'Comments Outlined'),
            array('class' => 'bolt', 'name' => 'Bolt'),
            array('class' => 'sitemap', 'name' => 'Sitemap'),
            array('class' => 'umbrella', 'name' => 'Umbrella'),
            array('class' => 'clipboard', 'name' => 'Clipboard'),
            array('class' => 'lightbulb-o', 'name' => 'Lightbulb Outlined'),
            array('class' => 'exchange', 'name' => 'Exchange'),
            array('class' => 'cloud-download', 'name' => 'Cloud Download'),
            array('class' => 'cloud-upload', 'name' => 'Cloud Upload'),
            array('class' => 'user-md', 'name' => 'User Md'),
            array('class' => 'stethoscope', 'name' => 'Stethoscope'),
            array('class' => 'suitcase', 'name' => 'Suitcase'),
            array('class' => 'bell-o', 'name' => 'Bell Outlined'),
            array('class' => 'coffee', 'name' => 'Coffee'),
            array('class' => 'cutlery', 'name' => 'Cutlery'),
            array('class' => 'file-text-o', 'name' => 'File Text Outlined'),
            array('class' => 'building-o', 'name' => 'Building Outlined'),
            array('class' => 'hospital-o', 'name' => 'Hospital Outlined'),
            array('class' => 'ambulance', 'name' => 'Ambulance'),
            array('class' => 'medkit', 'name' => 'Medkit'),
            array('class' => 'fighter-jet', 'name' => 'Fighter Jet'),
            array('class' => 'beer', 'name' => 'Beer'),
            array('class' => 'h-square', 'name' => 'H Square'),
            array('class' => 'plus-square', 'name' => 'Plus Square'),
            array('class' => 'angle-double-left', 'name' => 'Angle Double (Left)'),
            array('class' => 'angle-double-right', 'name' => 'Angle Double (Right)'),
            array('class' => 'angle-double-up', 'name' => 'Angle Double (Up)'),
            array('class' => 'angle-double-down', 'name' => 'Angle Double (Down)'),
            array('class' => 'angle-left', 'name' => 'Angle (Left)'),
            array('class' => 'angle-right', 'name' => 'Angle (Right)'),
            array('class' => 'angle-up', 'name' => 'Angle (Up)'),
            array('class' => 'angle-down', 'name' => 'Angle (Down)'),
            array('class' => 'desktop', 'name' => 'Desktop'),
            array('class' => 'laptop', 'name' => 'Laptop'),
            array('class' => 'tablet', 'name' => 'Tablet'),
            array('class' => 'mobile', 'name' => 'Mobile'),
            array('class' => 'circle-o', 'name' => 'Circle Outlined'),
            array('class' => 'quote-left', 'name' => 'Quote (Left)'),
            array('class' => 'quote-right', 'name' => 'Quote (Right)'),
            array('class' => 'spinner', 'name' => 'Spinner'),
            array('class' => 'circle', 'name' => 'Circle'),
            array('class' => 'reply', 'name' => 'Reply'),
            array('class' => 'github-alt', 'name' => 'Github Alt'),
            array('class' => 'folder-o', 'name' => 'Folder Outlined'),
            array('class' => 'folder-open-o', 'name' => 'Folder Open Outlined'),
            array('class' => 'smile-o', 'name' => 'Smile Outlined'),
            array('class' => 'frown-o', 'name' => 'Frown Outlined'),
            array('class' => 'meh-o', 'name' => 'Meh Outlined'),
            array('class' => 'gamepad', 'name' => 'Gamepad'),
            array('class' => 'keyboard-o', 'name' => 'Keyboard Outlined'),
            array('class' => 'flag-o', 'name' => 'Flag Outlined'),
            array('class' => 'flag-checkered', 'name' => 'Flag Checkered'),
            array('class' => 'terminal', 'name' => 'Terminal'),
            array('class' => 'code', 'name' => 'Code'),
            array('class' => 'reply-all', 'name' => 'Reply All'),
            array('class' => 'star-half-o', 'name' => 'Star Half Outlined'),
            array('class' => 'location-arrow', 'name' => 'Location Arrow'),
            array('class' => 'crop', 'name' => 'Crop'),
            array('class' => 'code-fork', 'name' => 'Code Fork'),
            array('class' => 'chain-broken', 'name' => 'Chain Broken'),
            array('class' => 'question', 'name' => 'Question'),
            array('class' => 'info', 'name' => 'Info'),
            array('class' => 'exclamation', 'name' => 'Exclamation'),
            array('class' => 'superscript', 'name' => 'Superscript'),
            array('class' => 'subscript', 'name' => 'Subscript'),
            array('class' => 'eraser', 'name' => 'Eraser'),
            array('class' => 'puzzle-piece', 'name' => 'Puzzle Piece'),
            array('class' => 'microphone', 'name' => 'Microphone'),
            array('class' => 'microphone-slash', 'name' => 'Microphone Slash'),
            array('class' => 'shield', 'name' => 'Shield'),
            array('class' => 'calendar-o', 'name' => 'Calendar Outlined'),
            array('class' => 'fire-extinguisher', 'name' => 'Fire Extinguisher'),
            array('class' => 'rocket', 'name' => 'Rocket'),
            array('class' => 'maxcdn', 'name' => 'Maxcdn'),
            array('class' => 'chevron-circle-left', 'name' => 'Chevron Circle (Left)'),
            array('class' => 'chevron-circle-right', 'name' => 'Chevron Circle (Right)'),
            array('class' => 'chevron-circle-up', 'name' => 'Chevron Circle (Up)'),
            array('class' => 'chevron-circle-down', 'name' => 'Chevron Circle (Down)'),
            array('class' => 'html5', 'name' => 'Html5'),
            array('class' => 'css3', 'name' => 'Css3'),
            array('class' => 'anchor', 'name' => 'Anchor'),
            array('class' => 'unlock-alt', 'name' => 'Unlock Alt'),
            array('class' => 'bullseye', 'name' => 'Bullseye'),
            array('class' => 'ellipsis-h', 'name' => 'Ellipsis H'),
            array('class' => 'ellipsis-v', 'name' => 'Ellipsis V'),
            array('class' => 'rss-square', 'name' => 'Rss Square'),
            array('class' => 'play-circle', 'name' => 'Play Circle'),
            array('class' => 'ticket', 'name' => 'Ticket'),
            array('class' => 'minus-square', 'name' => 'Minus Square'),
            array('class' => 'minus-square-o', 'name' => 'Minus Square Outlined'),
            array('class' => 'level-up', 'name' => 'Level (Up)'),
            array('class' => 'level-down', 'name' => 'Level (Down)'),
            array('class' => 'check-square', 'name' => 'Check Square'),
            array('class' => 'pencil-square', 'name' => 'Pencil Square'),
            array('class' => 'external-link-square', 'name' => 'External Link Square'),
            array('class' => 'share-square', 'name' => 'Share Square'),
            array('class' => 'compass', 'name' => 'Compass'),
            array('class' => 'caret-square-o-down', 'name' => 'Caret Square Outlined (Down)'),
            array('class' => 'caret-square-o-up', 'name' => 'Caret Square Outlined (Up)'),
            array('class' => 'caret-square-o-right', 'name' => 'Caret Square Outlined (Right)'),
            array('class' => 'eur', 'name' => 'Eur'),
            array('class' => 'gbp', 'name' => 'Gbp'),
            array('class' => 'usd', 'name' => 'Usd'),
            array('class' => 'inr', 'name' => 'Inr'),
            array('class' => 'jpy', 'name' => 'Jpy'),
            array('class' => 'rub', 'name' => 'Rub'),
            array('class' => 'krw', 'name' => 'Krw'),
            array('class' => 'btc', 'name' => 'Btc'),
            array('class' => 'file', 'name' => 'File'),
            array('class' => 'file-text', 'name' => 'File Text'),
            array('class' => 'sort-alpha-asc', 'name' => 'Sort Alpha Asc'),
            array('class' => 'sort-alpha-desc', 'name' => 'Sort Alpha Desc'),
            array('class' => 'sort-amount-asc', 'name' => 'Sort Amount Asc'),
            array('class' => 'sort-amount-desc', 'name' => 'Sort Amount Desc'),
            array('class' => 'sort-numeric-asc', 'name' => 'Sort Numeric Asc'),
            array('class' => 'sort-numeric-desc', 'name' => 'Sort Numeric Desc'),
            array('class' => 'thumbs-up', 'name' => 'Thumbs (Up)'),
            array('class' => 'thumbs-down', 'name' => 'Thumbs (Down)'),
            array('class' => 'youtube-square', 'name' => 'Youtube Square'),
            array('class' => 'youtube', 'name' => 'Youtube'),
            array('class' => 'xing', 'name' => 'Xing'),
            array('class' => 'xing-square', 'name' => 'Xing Square'),
            array('class' => 'youtube-play', 'name' => 'Youtube Play'),
            array('class' => 'dropbox', 'name' => 'Dropbox'),
            array('class' => 'stack-overflow', 'name' => 'Stack Overflow'),
            array('class' => 'instagram', 'name' => 'Instagram'),
            array('class' => 'flickr', 'name' => 'Flickr'),
            array('class' => 'adn', 'name' => 'Adn'),
            array('class' => 'bitbucket', 'name' => 'Bitbucket'),
            array('class' => 'bitbucket-square', 'name' => 'Bitbucket Square'),
            array('class' => 'tumblr', 'name' => 'Tumblr'),
            array('class' => 'tumblr-square', 'name' => 'Tumblr Square'),
            array('class' => 'long-arrow-down', 'name' => 'Long Arrow (Down)'),
            array('class' => 'long-arrow-up', 'name' => 'Long Arrow (Up)'),
            array('class' => 'long-arrow-left', 'name' => 'Long Arrow (Left)'),
            array('class' => 'long-arrow-right', 'name' => 'Long Arrow (Right)'),
            array('class' => 'apple', 'name' => 'Apple'),
            array('class' => 'windows', 'name' => 'Windows'),
            array('class' => 'android', 'name' => 'Android'),
            array('class' => 'linux', 'name' => 'Linux'),
            array('class' => 'dribbble', 'name' => 'Dribbble'),
            array('class' => 'skype', 'name' => 'Skype'),
            array('class' => 'foursquare', 'name' => 'Foursquare'),
            array('class' => 'trello', 'name' => 'Trello'),
            array('class' => 'female', 'name' => 'Female'),
            array('class' => 'male', 'name' => 'Male'),
            array('class' => 'gratipay', 'name' => 'Gratipay'),
            array('class' => 'sun-o', 'name' => 'Sun Outlined'),
            array('class' => 'moon-o', 'name' => 'Moon Outlined'),
            array('class' => 'archive', 'name' => 'Archive'),
            array('class' => 'bug', 'name' => 'Bug'),
            array('class' => 'vk', 'name' => 'Vk'),
            array('class' => 'weibo', 'name' => 'Weibo'),
            array('class' => 'renren', 'name' => 'Renren'),
            array('class' => 'pagelines', 'name' => 'Pagelines'),
            array('class' => 'stack-exchange', 'name' => 'Stack Exchange'),
            array('class' => 'arrow-circle-o-right', 'name' => 'Arrow Circle Outlined (Right)'),
            array('class' => 'arrow-circle-o-left', 'name' => 'Arrow Circle Outlined (Left)'),
            array('class' => 'caret-square-o-left', 'name' => 'Caret Square Outlined (Left)'),
            array('class' => 'dot-circle-o', 'name' => 'Dot Circle Outlined'),
            array('class' => 'wheelchair', 'name' => 'Wheelchair'),
            array('class' => 'vimeo-square', 'name' => 'Vimeo Square'),
            array('class' => 'try', 'name' => 'Try'),
            array('class' => 'plus-square-o', 'name' => 'Plus Square Outlined'),
            array('class' => 'space-shuttle', 'name' => 'Space Shuttle'),
            array('class' => 'slack', 'name' => 'Slack'),
            array('class' => 'envelope-square', 'name' => 'Envelope Square'),
            array('class' => 'wordpress', 'name' => 'Wordpress'),
            array('class' => 'openid', 'name' => 'Openid'),
            array('class' => 'university', 'name' => 'University'),
            array('class' => 'graduation-cap', 'name' => 'Graduation Cap'),
            array('class' => 'yahoo', 'name' => 'Yahoo'),
            array('class' => 'google', 'name' => 'Google'),
            array('class' => 'reddit', 'name' => 'Reddit'),
            array('class' => 'reddit-square', 'name' => 'Reddit Square'),
            array('class' => 'stumbleupon-circle', 'name' => 'Stumbleupon Circle'),
            array('class' => 'stumbleupon', 'name' => 'Stumbleupon'),
            array('class' => 'delicious', 'name' => 'Delicious'),
            array('class' => 'digg', 'name' => 'Digg'),
            array('class' => 'pied-piper', 'name' => 'Pied Piper'),
            array('class' => 'pied-piper-alt', 'name' => 'Pied Piper Alt'),
            array('class' => 'drupal', 'name' => 'Drupal'),
            array('class' => 'joomla', 'name' => 'Joomla'),
            array('class' => 'language', 'name' => 'Language'),
            array('class' => 'fax', 'name' => 'Fax'),
            array('class' => 'building', 'name' => 'Building'),
            array('class' => 'child', 'name' => 'Child'),
            array('class' => 'paw', 'name' => 'Paw'),
            array('class' => 'spoon', 'name' => 'Spoon'),
            array('class' => 'cube', 'name' => 'Cube'),
            array('class' => 'cubes', 'name' => 'Cubes'),
            array('class' => 'behance', 'name' => 'Behance'),
            array('class' => 'behance-square', 'name' => 'Behance Square'),
            array('class' => 'steam', 'name' => 'Steam'),
            array('class' => 'steam-square', 'name' => 'Steam Square'),
            array('class' => 'recycle', 'name' => 'Recycle'),
            array('class' => 'car', 'name' => 'Car'),
            array('class' => 'taxi', 'name' => 'Taxi'),
            array('class' => 'tree', 'name' => 'Tree'),
            array('class' => 'spotify', 'name' => 'Spotify'),
            array('class' => 'deviantart', 'name' => 'Deviantart'),
            array('class' => 'soundcloud', 'name' => 'Soundcloud'),
            array('class' => 'database', 'name' => 'Database'),
            array('class' => 'file-pdf-o', 'name' => 'File Pdf Outlined'),
            array('class' => 'file-word-o', 'name' => 'File Word Outlined'),
            array('class' => 'file-excel-o', 'name' => 'File Excel Outlined'),
            array('class' => 'file-powerpoint-o', 'name' => 'File Powerpoint Outlined'),
            array('class' => 'file-image-o', 'name' => 'File Image Outlined'),
            array('class' => 'file-archive-o', 'name' => 'File Archive Outlined'),
            array('class' => 'file-audio-o', 'name' => 'File Audio Outlined'),
            array('class' => 'file-video-o', 'name' => 'File Video Outlined'),
            array('class' => 'file-code-o', 'name' => 'File Code Outlined'),
            array('class' => 'vine', 'name' => 'Vine'),
            array('class' => 'codepen', 'name' => 'Codepen'),
            array('class' => 'jsfiddle', 'name' => 'Jsfiddle'),
            array('class' => 'life-ring', 'name' => 'Life Ring'),
            array('class' => 'circle-o-notch', 'name' => 'Circle Outlined Notch'),
            array('class' => 'rebel', 'name' => 'Rebel'),
            array('class' => 'empire', 'name' => 'Empire'),
            array('class' => 'git-square', 'name' => 'Git Square'),
            array('class' => 'git', 'name' => 'Git'),
            array('class' => 'hacker-news', 'name' => 'Hacker News'),
            array('class' => 'tencent-weibo', 'name' => 'Tencent Weibo'),
            array('class' => 'qq', 'name' => 'Qq'),
            array('class' => 'weixin', 'name' => 'Weixin'),
            array('class' => 'paper-plane', 'name' => 'Paper Plane'),
            array('class' => 'paper-plane-o', 'name' => 'Paper Plane Outlined'),
            array('class' => 'history', 'name' => 'History'),
            array('class' => 'circle-thin', 'name' => 'Circle Thin'),
            array('class' => 'header', 'name' => 'Header'),
            array('class' => 'paragraph', 'name' => 'Paragraph'),
            array('class' => 'sliders', 'name' => 'Sliders'),
            array('class' => 'share-alt', 'name' => 'Share Alt'),
            array('class' => 'share-alt-square', 'name' => 'Share Alt Square'),
            array('class' => 'bomb', 'name' => 'Bomb'),
            array('class' => 'futbol-o', 'name' => 'Futbol Outlined'),
            array('class' => 'tty', 'name' => 'Tty'),
            array('class' => 'binoculars', 'name' => 'Binoculars'),
            array('class' => 'plug', 'name' => 'Plug'),
            array('class' => 'slideshare', 'name' => 'Slideshare'),
            array('class' => 'twitch', 'name' => 'Twitch'),
            array('class' => 'yelp', 'name' => 'Yelp'),
            array('class' => 'newspaper-o', 'name' => 'Newspaper Outlined'),
            array('class' => 'wifi', 'name' => 'Wifi'),
            array('class' => 'calculator', 'name' => 'Calculator'),
            array('class' => 'paypal', 'name' => 'Paypal'),
            array('class' => 'google-wallet', 'name' => 'Google Wallet'),
            array('class' => 'cc-visa', 'name' => 'Cc Visa'),
            array('class' => 'cc-mastercard', 'name' => 'Cc Mastercard'),
            array('class' => 'cc-discover', 'name' => 'Cc Discover'),
            array('class' => 'cc-amex', 'name' => 'Cc Amex'),
            array('class' => 'cc-paypal', 'name' => 'Cc Paypal'),
            array('class' => 'cc-stripe', 'name' => 'Cc Stripe'),
            array('class' => 'bell-slash', 'name' => 'Bell Slash'),
            array('class' => 'bell-slash-o', 'name' => 'Bell Slash Outlined'),
            array('class' => 'trash', 'name' => 'Trash'),
            array('class' => 'copyright', 'name' => 'Copy(Right)'),
            array('class' => 'at', 'name' => 'At'),
            array('class' => 'eyedropper', 'name' => 'Eyedropper'),
            array('class' => 'paint-brush', 'name' => 'Paint Brush'),
            array('class' => 'birthday-cake', 'name' => 'Birthday Cake'),
            array('class' => 'area-chart', 'name' => 'Area Chart'),
            array('class' => 'pie-chart', 'name' => 'Pie Chart'),
            array('class' => 'line-chart', 'name' => 'Line Chart'),
            array('class' => 'lastfm', 'name' => 'Lastfm'),
            array('class' => 'lastfm-square', 'name' => 'Lastfm Square'),
            array('class' => 'toggle-off', 'name' => 'Toggle Off'),
            array('class' => 'toggle-on', 'name' => 'Toggle On'),
            array('class' => 'bicycle', 'name' => 'Bicycle'),
            array('class' => 'bus', 'name' => 'Bus'),
            array('class' => 'ioxhost', 'name' => 'Ioxhost'),
            array('class' => 'angellist', 'name' => 'Angellist'),
            array('class' => 'cc', 'name' => 'Cc'),
            array('class' => 'ils', 'name' => 'Ils'),
            array('class' => 'meanpath', 'name' => 'Meanpath'),
            array('class' => 'buysellads', 'name' => 'Buysellads'),
            array('class' => 'connectdevelop', 'name' => 'Connectdevelop'),
            array('class' => 'dashcube', 'name' => 'Dashcube'),
            array('class' => 'forumbee', 'name' => 'Forumbee'),
            array('class' => 'leanpub', 'name' => 'Leanpub'),
            array('class' => 'sellsy', 'name' => 'Sellsy'),
            array('class' => 'shirtsinbulk', 'name' => 'Shirtsinbulk'),
            array('class' => 'simplybuilt', 'name' => 'Simplybuilt'),
            array('class' => 'skyatlas', 'name' => 'Skyatlas'),
            array('class' => 'cart-plus', 'name' => 'Cart Plus'),
            array('class' => 'cart-arrow-down', 'name' => 'Cart Arrow (Down)'),
            array('class' => 'diamond', 'name' => 'Diamond'),
            array('class' => 'ship', 'name' => 'Ship'),
            array('class' => 'user-secret', 'name' => 'User Secret'),
            array('class' => 'motorcycle', 'name' => 'Motorcycle'),
            array('class' => 'street-view', 'name' => 'Street View'),
            array('class' => 'heartbeat', 'name' => 'Heartbeat'),
            array('class' => 'venus', 'name' => 'Venus'),
            array('class' => 'mars', 'name' => 'Mars'),
            array('class' => 'mercury', 'name' => 'Mercury'),
            array('class' => 'transgender', 'name' => 'Transgender'),
            array('class' => 'transgender-alt', 'name' => 'Transgender Alt'),
            array('class' => 'venus-double', 'name' => 'Venus Double'),
            array('class' => 'mars-double', 'name' => 'Mars Double'),
            array('class' => 'venus-mars', 'name' => 'Venus Mars'),
            array('class' => 'mars-stroke', 'name' => 'Mars Stroke'),
            array('class' => 'mars-stroke-v', 'name' => 'Mars Stroke V'),
            array('class' => 'mars-stroke-h', 'name' => 'Mars Stroke H'),
            array('class' => 'neuter', 'name' => 'Neuter'),
            array('class' => 'facebook-official', 'name' => 'Facebook Official'),
            array('class' => 'pinterest-p', 'name' => 'Pinterest P'),
            array('class' => 'whatsapp', 'name' => 'Whatsapp'),
            array('class' => 'server', 'name' => 'Server'),
            array('class' => 'user-plus', 'name' => 'User Plus'),
            array('class' => 'user-times', 'name' => 'User Times'),
            array('class' => 'bed', 'name' => 'Bed'),
            array('class' => 'viacoin', 'name' => 'Viacoin'),
            array('class' => 'train', 'name' => 'Train'),
            array('class' => 'subway', 'name' => 'Subway'),
            array('class' => 'medium', 'name' => 'Medium'),
        );
        $help_icons = '<div style="clear: both;"></div>';
        $help_icons .= '<div style="text-align: justify;">With this plugin you can change the menu order and you can add custom menu items. '
                . 'To reset the menu just click on the red "RESET MENUS" button. '
                . '</div>';
        $help_icons .= '<div><b>We have ' . count($fa_icons) . ' icons :-)</b></div>'
                . '<br/><div><center><b><i>Please note: This plugin is working ONLY with the AdminLTE theme!</i></b></center></div><hr>';
        foreach ($fa_icons as $icon) {
            $selected = ($selected_icon == $icon['class']) ? 'selected' : '';
            $select_icons .= '<option ' . $selected . ' value="' . $icon['class'] . '">' . $icon['name'] . '</option>';
            $help_icons .= '<div style="float: left; width: 200px; padding: 5px;"><i class="fa fa-' . $icon['class'] . '"></i>&nbsp;&nbsp;' . $icon['name'] . '</div>';
        }

        return ($help) ? $help_icons : $select_icons;

        /* END public function get_select_FontAwesomeIcons($selected_icon, $help = false) */
    }

    /* END class module_webnpro_menu_editor */
}
