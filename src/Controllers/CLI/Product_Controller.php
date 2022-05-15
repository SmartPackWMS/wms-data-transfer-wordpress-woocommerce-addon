<?php

namespace SmartPack\WMS\Controllers\CLI;

use Exception;
use SmartPack\WMS\WMSApi\Webhook;

class CLI_Products
{
    function getProtectedValue($obj, $name) {
        $array = (array)$obj;
        $prefix = chr(0).'*'.chr(0);
        return $array[$prefix.$name];
    }

    function execute()
    {
        echo 'Start product sync';

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
                $wc_product_data = \wc_get_product( $product->ID );
                $image_id  = $wc_product_data->get_image_id();
                $image_url = \wp_get_attachment_image_url( $image_id, 'full' );
                
                $obj_product = $this->getProtectedValue($wc_product_data, 'data');
                
                $product_data = [
                    'method' => 'product',
                    'data' => [
                        'id' => (string) $product->ID,
                        'sku' => $obj_product['sku'],
                        'title' => $obj_product['name'],
                        'description' => $obj_product['description'],
                        'cost' => $obj_product['price'],
                        'vendor' => '',
                        'manufacturer' => '',
                        'weight' => $obj_product['weight'],
                        'imageUrl' => $image_url
                    ]
                ];

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
