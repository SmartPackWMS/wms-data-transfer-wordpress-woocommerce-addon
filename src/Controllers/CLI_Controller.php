<?php

namespace SmartPack\WMS\Controllers;

use SmartPack\WMS\Controllers\CLI\CLI_Products;

class CLI
{
    function init()
    {
        add_action('cli_init', function () {
            $product_cli = new CLI_Products();

            \WP_CLI::add_command('smartpack:product:sync', [$product_cli, 'execute']);
            // \WP_CLI::add_command('smartpack:stock:sync', [$product_cli, 'hello_world']);
            // \WP_CLI::add_command('smartpack:order:sync', [$product_cli, 'hello_world']);
        });
    }
}
