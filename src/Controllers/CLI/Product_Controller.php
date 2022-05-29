<?php

namespace SmartPack\WMS\Controllers\CLI;

use Exception;
use SmartPack\WMS\WMSApi\Webhook;
use SmartPack\WMS\Helpers;

class CLI_Products
{
    function execute()
    {
        echo 'Start product sync';

        $webhook = new Webhook();

        $all_ids = get_posts([
            'post_type' => ['product'],
            'numberposts' => -1,
            'post_status' => 'publish',
            'meta_query' => [[
                'key' => 'smartpack_wms_state',
                'value' => 'pending'
            ]]
        ]);

        foreach ($all_ids as $product) {
            $product_sku = \get_post_meta($product->ID, '_sku', true);
            
            $product_type = null;
            $product_data = [];
            $woo_product = wc_get_product( $product->ID );

            if ($woo_product->is_type('simple')) {
                $product_type = 'simple';
                $product_data[] = [
                    'method' => 'product',
                    'data' => Helpers::getProductData($product->ID)
                ];

            } elseif ($woo_product->is_type('variable')) {
                $product_type = 'variable';
                $product_data[] = [
                    'method' => 'product',
                    'data' => Helpers::getProductData($product->ID)
                ];

                $product_variation = get_posts([
                    'post_type' => ['product_variation'],
                    'numberposts' => -1,
                    'post_status' => 'publish',
                    'post_parent' => $product->ID
                ]);

                foreach ($product_variation as $variation) {
                    $product_data[] = [
                        'method' => 'product',
                        'data' => Helpers::getProductData($product->ID)
                    ];
                }
               
            } else {
                echo 'prodct type missing!';
            }

            if ($product_sku && $product_type !== null) {
                try {
                    $response = $webhook->push($product_data);
                    if ($response['statusCode'] === 201) {
                        update_post_meta($product->ID, 'smartpack_wms_state', 'synced');
                        update_post_meta($product->ID, 'smartpack_wms_changed', new \DateTime());

                        echo '[' . $product->ID . '] [' . $product_sku . '] Product synced to Smartpack WMS';
                    } else {
                        echo '[' . $product->ID . '] [' . $product_sku . '] Product not synced to Smartpack WMS, status code: ' . $response['statusCode'];
                    }
                } catch (Exception $error) {
                    echo 'Missing connection to SmartPack API - Look trace error below';
                }
            } else {
                echo 'Product missing SKU number';
            }
        }
    }
}
