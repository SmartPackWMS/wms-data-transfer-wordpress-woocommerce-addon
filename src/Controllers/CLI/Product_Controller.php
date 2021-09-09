<?php

namespace SmartPack\WMS\Controllers\CLI;

use Exception;
use SmartPack\WMS\WMSApi\Items;

class CLI_Products
{
    function execute()
    {
        \WP_CLI::line('Start product sync');

        $items = new Items();

        $all_ids = get_posts([
            'post_type' => 'product',
            'numberposts' => -1,
            'post_status' => 'publish'
        ]);

        foreach ($all_ids as $product) {
            print_r($product);
            $product_sku = \get_post_meta($product->ID, '_sku', true);

            if ($product_sku) {
                $item = [
                    'sku' => $product_sku,
                    'description' => $product->post_title
                ];

                try {
                    $items->import($item);
                    \WP_CLI::success('Product synced to Smartpack WMS');
                } catch (Exception $error) {
                    \WP_CLI::warning('Missing connection to SmartPack API - Look trace error below');
                    print_r($error);
                }
            } else {
                \WP_CLI::error('Product missing SKU number');
            }
        }
    }
}
