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
            'post_status' => 'publish',
            'meta_query' => [[
                'key' => 'smartpack_wms_state',
                'value' => 'pending'
            ]]
        ]);

        foreach ($all_ids as $product) {
            $product_sku = \get_post_meta($product->ID, '_sku', true);

            if ($product_sku) {
                $item = [
                    'method' => 'product',
                    'data' => [
                        'sku' => $product_sku,
                        'description' => $product->post_title
                    ]
                ];

                try {
                    $items->import($item);
                    //             update_post_meta($product->ID, 'smartpack_wms_state', 'synced');
                    //             update_post_meta($product->ID, 'smartpack_wms_changed', new \DateTime());

                    \WP_CLI::success('[' . $product->ID . '] [' . $product_sku . '] Product synced to Smartpack WMS');
                } catch (Exception $error) {
                    \WP_CLI::warning('Missing connection to SmartPack API - Look trace error below');
                }
            } else {
                \WP_CLI::error('Product missing SKU number');
            }
        }
    }
}
