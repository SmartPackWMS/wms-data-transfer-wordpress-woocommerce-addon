<?php

namespace SmartPack\WMS;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class Helpers
{
    static function wcGetOrderLineMeta(int $order_item_id, string $meta_key)
    {
        global $wpdb;

        $meta_data = $wpdb->get_row("
         SELECT 
             *    

         FROM 
             wp_woocommerce_order_itemmeta

         WHERE
             order_item_id = " . $order_item_id . " AND
             meta_key = '" . $meta_key . "'

            
         ");

        return $meta_data;
    }
}
