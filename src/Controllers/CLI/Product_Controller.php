<?php

namespace SmartPack\WMS\Controllers\CLI;

use SmartPack\WMS\WMSApi\Items;

class CLI_Products
{
    function execute()
    {
        \WP_CLI::info('Start product sync');

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
                $items->import($item);

                \WP_CLI::success('Product synced to Smartpack WMS');
            } else {
                \WP_CLI::danger('Product missing SKU number');
            }
        }
    }
}
