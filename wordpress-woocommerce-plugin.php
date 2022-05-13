<?php

/**
 * Plugin Name: SmartPack - WMS Data Integration 
 * Description: Full integration with SmartPack WMS API when using WooCommerce as shopping platform.
 * Author: SmartPack
 * Author URI: https://smartpack.dk
 * Version: 0.0.2
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('ABSPATH') || exit;

define('SMARTPACK_WMS_PLUGIN_DIR', plugin_dir_path(__FILE__));

// adding src folder files
require_once 'src/helpers/helpers.php';
require_once 'src/WMSApi/APIService.php';
require_once 'src/WMSApi/Webhook.php';
require_once 'src/Controllers/WPInit_Controller.php';
require_once 'src/Controllers/AdminSettingsPage_Controller.php';
require_once 'src/Controllers/CLI_Controller.php';
require_once 'src/Controllers/CLI/Product_Controller.php';
require_once 'src/Controllers/CLI/Order_Controller.php';
require_once 'src/Controllers/CLI/Stock_Controller.php';
require_once 'src/Controllers/RestRoutes_Controller.php';

$restController = new SmartPack\WMS\Controllers\RestRoutes_Controller();
$restController->init();

$wpInitController = new SmartPack\WMS\Controllers\WPInit_Controller();
$wpInitController->init();

$adminSettingsPageController = new SmartPack\WMS\Controllers\AdminSettingsPage_Controller();
$adminSettingsPageController->init();

$CLIController = new SmartPack\WMS\Controllers\CLI();
$CLIController->init();
