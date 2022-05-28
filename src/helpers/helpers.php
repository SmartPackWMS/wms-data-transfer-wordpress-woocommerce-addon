<?php

namespace SmartPack\WMS;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class Helpers
{
   static  function getProtectedValue($obj, $name) {
        $array = (array)$obj;
        $prefix = chr(0).'*'.chr(0);
        return $array[$prefix.$name];
    }

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

    static function getAllproducts($limit=100, $offset=0) {
        $products = get_posts([
            'post_type' => ['product', 'product_variation'],
            'posts_per_page' => $limit,
            'offset' => $offset,
            'post_status' => 'publish',
        ]);
        
        return $products;
    }

    static function getProductData($product_id) {
        $wc_product_data = \wc_get_product( $product_id );
        $image_id  = $wc_product_data->get_image_id();
        $image_url = \wp_get_attachment_image_url( $image_id, 'full' );
        
        $obj_product = self::getProtectedValue($wc_product_data, 'data');
        
        $product_data = [
            'id' => (string) $product_id,
            'sku' => $obj_product['sku'],
            'title' => $obj_product['name'],
            'description' => $obj_product['description'],
            'cost' => $obj_product['price'],
            'vendor' => '',
            'manufacturer' => '',
            'weight' => $obj_product['weight'],
            'imageUrl' => $image_url
        ];

        return $product_data;
    }
}
