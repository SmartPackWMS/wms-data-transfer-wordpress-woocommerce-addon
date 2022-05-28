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

    static function getAllOrders($limit=100, $offset=0) {
        $orders = get_posts([
            'post_type' => 'shop_order',
            'posts_per_page' => $limit,
            'offset' => $offset,
            'post_status' => [
                'wc-processing'
            ]
        ]);

        return $orders;
    }

    static function getOrderData($order_id, $order) {
        global $wpdb;

        $lines = $wpdb->get_results("
        SELECT 
            *    
        
        FROM 
            wp_woocommerce_order_items
            
        WHERE
            order_id = " . $order_id . " AND
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

        $shipment_firstname = \get_post_meta($order_id, '_shipping_first_name', true);
        $shipment_lastname = \get_post_meta($order_id, '_shipping_last_name', true);
        $shipment_company = \get_post_meta($order_id, '_shipping_company', true);
        $shipment_address_1 = \get_post_meta($order_id, '_shipping_address_1', true);
        $shipment_city = \get_post_meta($order_id, '_shipping_city', true);
        $shipment_postcode = \get_post_meta($order_id, '_shipping_postcode', true);
        $shipment_country = \get_post_meta($order_id, '_shipping_country', true);
        $billing_email = \get_post_meta($order_id, '_billing_email', true);
        $billing_phone = \get_post_meta($order_id, '_billing_phone', true);

        $customer_ip_address = \get_post_meta($order_id, '_customer_ip_address', true);
        $customer_user_agent = \get_post_meta($order_id, '_customer_user_agent', true);
        $payment_method = \get_post_meta($order_id, '_payment_method_title', true);

        # _order_total
        # _order_key: wc_order_en5sptKclNffY

        $order_data = [
            'orderNo' => (string) $order_id,
            'referenceNo' => (string) $order_id,
            'uniqueReferenceNo' => (string) $order_id,
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
                'phone' => '98765432',
                'email' => 'asdfgh@dfghj.dk',
                'country' => 'Denmark',
                "country_code" => 'DK'
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
                'country' => 'Denmark',
                "country_code" => 'DK'
            ],
            "deliveryMethod" => 'none',
            "droppointId" => '',
            "items" => $order_lines
        ];

        return $order_data;
    }
}
