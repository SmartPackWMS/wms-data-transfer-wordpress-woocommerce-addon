<?php

namespace SmartPack\WMS\Controllers\CLI;

class CLI_Products
{
    public function hello_world()
    {
        \WP_CLI::line('Hello World!');
    }
}
