<?php

/**
 * Plugin Name: SmartPack - WooCommerce WMS Plugin
 * Description: Full integration with SmartPack WMS API when using WooCommerce as shopping platform.
 * Version: 1.0.0
 * Author: SmartPack
 * Author URI: https://smartpack.dk
 */

define('SMARTPACK_WMS_PLUGIN_DIR', plugin_dir_path(__FILE__));

// adding src folder files
require_once 'src/helpers/helpers.php';
require_once 'src/Controllers/AdminSettingsPage_Controller.php';

$adminSettingsPageController = new SmartPack\WMS\Controllers\AdminSettingsPage_Controller();
$adminSettingsPageController->init();
