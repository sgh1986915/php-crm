<?php

/*
 *  Class: Update
 *  Copyright: Kőrösi Zoltán | webNpro - hosting and design | http://webnpro.com | info@webnpro.com
 *  Contact / Feature request: info@webnpro.com (Hungarian / English)
 *  License: Please check CodeCanyon.net for license details.
 *  More license clarification available here:  http://codecanyon.net/wiki/support/legal-terms/licensing-terms/
 */

if (!class_exists("update")) {

    /**
     *  webNpro UCM plugin update class
     */
    class update {

        /**
         * Class construction (do nothing)
         */
        public function __construct() {
            // Do nothing
            /* END public function __construct() */
        }

        /**
         * Get the license info and give it back as string
         * @return string
         */
        public function get_license_info() {
            $license = $this->license();
            return $license['info'];
            /* END public function get_license_info() */
        }

        /**
         * Get the license info from the webNpro server
         * @return string
         */
        public function license() {
            $update_url = $this->updateURL();

            $info = parse_ini_file(dirname(__FILE__) . '/../plugin.info');
            $plugin_name = $info['modulename'];
            $plugin_ver = $info['version'];
            $plugin_id = $info['id'];
            $plugin_link = $info['link'];

            $api_url = "http://zeus.webnpro.com/api/api.php";

            $curlvars = "id=" . $plugin_id . "&ver=" . $plugin_ver . "&key=" . module_config::c($plugin_name . '_envato_license_number', 1);

            $ch = curl_init();
            $timeout = 5;
            curl_setopt($ch, CURLOPT_URL, $api_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_POST, count($curlvars));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $curlvars);
            $data = json_decode(curl_exec($ch), true);

            // Error handle
            if ($data == '') {
                // No connection, etc.
                $info = '<font color="red"><strong>' . _l('License server connection error. We will try it later...') . '</strong></font>';
            } else {
                // Api is ok.
                if ($data['verify-purchase']['valid']) {

                    $info = '<font color="green"><strong>' . _l('Valid License Key.') . '</strong></font> | ';
                    $info .= _l('Item ID') . ': ' . $data['verify-purchase']['item_id'] . ' | ';
                    $info .= $data['verify-purchase']['item_name'] . ' v' . $plugin_ver . ' | ';
                    $info .= ($data['latest'] > $plugin_ver) ? _l('Latest Version') . ': ' . $data['latest'] . ' | <span id="update-info"><a href="' . $update_url . '" id="update"><font color="red"><strong>' . _l('[ UPDATE? ]') . '</strong></font></a></span>' : '<font color="green"><strong>' . _l('[ NO UPDATE ]') . '</strong></font>';
                } else {
                    $info = '<font color="red"><strong>' . _l('Invalid license key!') . '</strong></font> | ' . _l('Please purchase the license here:') . ' <a href="' . $plugin_link . '">' . $plugin_link . '</a>';
                }
            }

            $data['info'] = $info . '<br>';
            curl_close($ch);

            return $data;
            /* END public function license() */
        }

        /**
         * Update the plugin and redirect back to the plugins settings page
         * @return string Messages about the updating procedure
         */
        public function update() {
            echo '<font color="green"><strong>' . _l('[ DOWNLOAD THE LATEST VERSION ]') . '</strong></font>';
            $plugin_directory = dirname(__FILE__) . '/../';
            $info = parse_ini_file(dirname(__FILE__) . '/../plugin.info');
            $plugin_name = $info['modulename'];
            $plugin_ver = $info['version'];
            $plugin_id = $info['id'];
            $api_url = "http://zeus.webnpro.com/api/api.php";
            $zipFile = $plugin_id . '.zip';
            $zipDir = dirname(__FILE__) . '/updates/';
            //Make the directory if we need to...
            if (!is_dir($zipDir)) {
                mkdir($zipDir, 0755, true);
            }

            $zipFile = $zipDir . $zipFile;

            $zipResource = fopen($zipFile, "w");

            $curlvars = "download=1&id=" . $plugin_id . "&ver=" . $plugin_ver . "&key=" . module_config::c($plugin_name . '_envato_license_number', 1);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $api_url);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_POST, count($curlvars));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $curlvars);
            curl_setopt($ch, CURLOPT_FAILONERROR, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_AUTOREFERER, true);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_FILE, $zipResource);
            $data = curl_exec($ch);

            curl_close($ch);
            fclose($zipFile);

            echo '<font color="green"><strong>' . _l('[ UPDATE THE FILES ]') . '</strong></font>';

            //Open The File And Do Stuff
            $zipHandle = zip_open($zipFile);

            while ($aF = zip_read($zipHandle)) {
                $thisFileName = zip_entry_name($aF);
                $thisFileDir = dirname($thisFileName);

                //Continue if its not a file
                if (substr($thisFileName, -1, 1) == '/') {
                    continue;
                }

                //Make the directory if we need to...
                if (!is_dir($plugin_directory . $thisFileDir)) {
                    mkdir($plugin_directory . $thisFileDir, 0755, true);
                }

                //Overwrite the file
                if (!is_dir($plugin_directory . $thisFileName)) {

                    $contents = zip_entry_read($aF, zip_entry_filesize($aF));
                    $file_ext = array_pop(explode(".", $thisFileName));
                    $ext_ignore = array('png', 'jpg', 'gif');
                    if (!in_array($file_ext, $ext_ignore)) {
                        $contents = str_replace("\r\n", "\n", $contents);
                    }
                    $updateThis = '';

                    $updateThis = fopen($plugin_directory . $thisFileName, 'w');
                    fwrite($updateThis, $contents);
                    fclose($updateThis);
                    unset($contents);
                }
            }

            //If we need to run commands, then do it.
            if (is_file($plugin_directory . '/_update.php')) {
                echo '<font color="green"><strong>' . _l('[ RUN UPDATE SCRIPT ]') . '</strong></font>';
                include ($plugin_directory . '/_update.php');
                unlink($plugin_directory . '/_update.php');
            };

            // Delete the downloaded zip file
            if (is_file($zipFile)) {
                unlink($zipFile);
            };

            // Set the new plugin version in the config table
            module_config::save_config('_plugin_version_' . $plugin_name, $plugin_ver);
            echo '<font color="green"><strong>' . _l('[ PLUGIN UPDATED ]') . '</strong></font><br>';
            echo '<font color="red"><strong><a style="color: red; font-weight: bold;" href="' . $this->settingsURL() . '">' . _l('[ REDIRECT BACK TO THE PLUGINS SETTINGS PAGE IN 5 SECONDS ]') . '</a></strong></font>';
            header("Refresh: 5;url=" . $this->settingsURL());
            return $output;
            /* END public function update() */
        }

        /**
         * Get the plugins update page url
         * @return string
         */
        private function updateURL() {
            $pageURL = 'http';
            if ($_SERVER["HTTPS"] == "on") {
                $pageURL .= "s";
            }
            $pageURL .= "://";
            if ($_SERVER["SERVER_PORT"] != "80") {
                $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
            } else {
                $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
            }
            $pageURL = str_replace("www.", "", $pageURL);
            $pageURL = str_replace("&p[1]=settings", "&p[1]=update", $pageURL);
            $pageURL = str_replace(".settings/", ".update/", $pageURL);
            return $pageURL;
            /* END private function updateURL() */
        }

        /**
         * Get the plugins settings page url
         * @return string
         */
        private function settingsURL() {
            $pageURL = 'http';
            if ($_SERVER["HTTPS"] == "on") {
                $pageURL .= "s";
            }
            $pageURL .= "://";
            if ($_SERVER["SERVER_PORT"] != "80") {
                $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
            } else {
                $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
            }
            $pageURL = str_replace("www.", "", $pageURL);
            $pageURL = str_replace("&p[1]=update", "&p[1]=settings", $pageURL);
            $pageURL = str_replace(".update/", ".settings/", $pageURL);
            return $pageURL;
            /* END private function settingsURL() */
        }

        /* END class update */
    }

    /* END if (!class_exists("update")) */
}
?>