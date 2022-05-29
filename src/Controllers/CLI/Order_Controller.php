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
            $shipment_data = [[
                'method' => 'order',
                'data' => Helpers::getOrderData($order->ID, $order)
            ]];

            $response = $webhook->push($shipment_data);
            
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
