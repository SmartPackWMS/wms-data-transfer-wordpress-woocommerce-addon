<?php

namespace SmartPack\WMS\Controllers\CLI;

use Exception;
use SmartPack\WMS\WMSApi\Webhook;

class CLI_Products
{
    function execute()
    {
        \WP_CLI::line('Start product sync');

        $webhook = new Webhook();

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
                $product = [
                    'method' => 'product',
                    'data' => [
                        'sku' => $product_sku,
                        'description' => $product->post_title
                    ]
                ];

                try {
                    $response = $webhook->push($product);
                    if ($response['statusCode'] === 200) {
                        update_post_meta($product->ID, 'smartpack_wms_state', 'synced');
                        update_post_meta($product->ID, 'smartpack_wms_changed', new \DateTime());

                        \WP_CLI::success('[' . $product->ID . '] [' . $product_sku . '] Product synced to Smartpack WMS');
                    } else {
                        \WP_CLI::error('[' . $product->ID . '] [' . $product_sku . '] Product not synced to Smartpack WMS, status code: ' . $response['statusCode']);
                    }
                } catch (Exception $error) {
                    \WP_CLI::warning('Missing connection to SmartPack API - Look trace error below');
                }
            } else {
                \WP_CLI::error('Product missing SKU number');
            }
        }
    }
}
