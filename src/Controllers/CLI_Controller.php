<?php

namespace SmartPack\WMS\Controllers;

use SmartPack\WMS\Controllers\CLI\CLI_Products;
use SmartPack\WMS\Controllers\CLI\CLI_Orders;
use SmartPack\WMS\Controllers\CLI\CLI_Stock;

class CLI
{
    function init()
    {
        add_action('cli_init', function () {
            $product_cli = new CLI_Products();
            $stock_cli = new CLI_Stock();
            $order_cli = new CLI_Orders();

            \WP_CLI::add_command('smartpack:product:sync', [$product_cli, 'execute']);
            \WP_CLI::add_command('smartpack:stock:sync', [$stock_cli, 'execute']);
            \WP_CLI::add_command('smartpack:order:sync', [$order_cli, 'execute']);
        });
    }
}
