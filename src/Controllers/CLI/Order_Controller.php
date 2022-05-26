<?php

namespace SmartPack\WMS\Controllers\CLI;

use SmartPack\WMS\WMSApi\Webhook;
use SmartPack\WMS\Helpers;

class CLI_Orders
{
    function execute()
    {
        echo 'Start order sync';

        $webhook = new Webhook();
        
        $all_ids = get_posts([
            'post_type' => 'shop_order',
            'numberposts' => -1,
            'post_status' => [
                'wc-processing', 'wc-on-hold', 'wc-pending', 'wc-completed', 
                'wc-cancelled', 'wc-refunded', 'wc-failed'
            ],
            'meta_query' => [[
                'key' => 'smartpack_wms_state',
                'value' => 'pending'
            ]]
        ]);

        foreach ($all_ids as $order) {
            global $wpdb;

            $lines = $wpdb->get_results("
            SELECT 
                *    
            
            FROM 
                wp_woocommerce_order_items
                
            WHERE
                order_id = " . $order->ID . " AND
                order_item_type IN('line_item')

            ORDER BY
                order_item_id ASC
            ");

            $order_lines = [];
            foreach ($lines as $line) {
                $qty = Helpers::wcGetOrderLineMeta($line->order_item_id, '_qty');
                $product_id = Helpers::wcGetOrderLineMeta($line->order_item_id, '_product_id');
                $line_total = Helpers::wcGetOrderLineMeta($line->order_item_id, '_line_total');
                $product_sku = \get_post_meta($product_id->meta_value, '_sku', true);
                # _variation_id

                $order_lines[] = [
                    'product_id' => (string) $product_id->meta_value,
                    'price_total' => (float) ((int) $line_total->meta_value),
                    'price' => (float) ((int) $line_total->meta_value/(int) $qty->meta_value),
                    'sku' => $product_sku,
                    'qty' => (int) $qty->meta_value,
                ];
            }

            $shipment_firstname = \get_post_meta($order->ID, '_shipping_first_name', true);
            $shipment_lastname = \get_post_meta($order->ID, '_shipping_last_name', true);
            $shipment_company = \get_post_meta($order->ID, '_shipping_company', true);
            $shipment_address_1 = \get_post_meta($order->ID, '_shipping_address_1', true);
            $shipment_city = \get_post_meta($order->ID, '_shipping_city', true);
            $shipment_postcode = \get_post_meta($order->ID, '_shipping_postcode', true);
            $shipment_country = \get_post_meta($order->ID, '_shipping_country', true);
            $billing_email = \get_post_meta($order->ID, '_billing_email', true);
            $billing_phone = \get_post_meta($order->ID, '_billing_phone', true);

            $customer_ip_address = \get_post_meta($order->ID, '_customer_ip_address', true);
            $customer_user_agent = \get_post_meta($order->ID, '_customer_user_agent', true);
            $payment_method = \get_post_meta($order->ID, '_payment_method_title', true);

            # _order_total
            # _order_key: wc_order_en5sptKclNffY

            $shipment_data = [
                'method' => 'order',
                'data' => [
                    'orderNo' => (string) $order->ID,
                    'referenceNo' => (string) $order->ID,
                    'uniqueReferenceNo' => (string) $order->ID,
                    'status' => $order->post_status,
                    'description' => $order->post_excerpt,
                    'printDeliveryNote' => false,
                    'payment' => [
                        'method' => $payment_method,
                        'paid' => 1,
                    ],
                    'client_details' => [
                        'browser_ip' => $customer_ip_address,
                        'user_agent' => $customer_user_agent,
                    ],
                    'sender' => [
                        'name' => get_bloginfo('name'),
                        'street1' => get_option('woocommerce_store_address'),
                        'zipcode' => get_option('woocommerce_store_postcode'),
                        'city' => get_option('woocommerce_store_city'),
                        'country' => get_option('woocommerce_default_country'),
                        'phone' => '',
                        'email' => '',
                    ],
                    'recipient' => [
                        'name' => $shipment_firstname . ' ' . $shipment_lastname,
                        'attention' => '',
                        'street1' => $shipment_address_1,
                        'zipcode' => $shipment_postcode,
                        'city' => $shipment_city,
                        'country' =>  $shipment_country,
                        'phone' => $billing_phone,
                        'email' => $billing_email,
                    ],
                    "deliveryMethod" => 'none',
                    "droppointId" => '',
                    "items" => $order_lines
                ]
            ];

            $response = $webhook->push($shipment_data);
            print_r($response);
            
            if ($response['statusCode'] === 201) {
                update_post_meta($order->ID, 'smartpack_wms_state', 'synced');
                update_post_meta($order->ID, 'smartpack_wms_changed', new \DateTime());

                echo '[' . $order->ID . '] Order synced to SmartPack WMS';
            } else {
                echo '[' . $order->ID . '] Order error in sync to SmartPack WMS, return status code ' . $response['statusCode'];
            }
        }
    }
}
