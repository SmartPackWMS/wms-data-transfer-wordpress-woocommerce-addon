<?php

namespace SmartPack\WMS\Controllers\CLI;

use Exception;
use SmartPack\WMS\WMSApi\Items;

class CLI_Stock
{
    function execute()
    {
        \WP_CLI::line('Start stock sync');

        $items = new Items();
        $products = $items->list();

        if ($products->status === 200) {
            echo 'success connection';
        } else {
            echo 'error';
        }
        var_dump($products);
        foreach ($products as $val) {
            print_r($val);
        }
    }
}
