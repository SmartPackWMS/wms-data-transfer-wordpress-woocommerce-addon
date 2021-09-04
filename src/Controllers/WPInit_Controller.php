<?php

namespace SmartPack\WMS\Controllers;

use DateTime;

class WPInit_Controller
{
    function init()
    {
        add_action('woocommerce_update_product', function ($post_id) {
            update_post_meta($post_id, 'smartpack_wms_state', 'pending');
            update_post_meta($post_id, 'smartpack_wms_changed', new DateTime());
        }, 10, 1);

        // Order Status Change
        add_action('woocommerce_order_status_pending', function ($order_id) {
            // pending
        });
        add_action('woocommerce_order_status_failed', function ($order_id) {
            // failed
        });

        add_action('woocommerce_order_status_on-hold', function ($order_id) {
            // on-hold
        });

        add_action('woocommerce_order_status_processing', function ($order_id) {
            update_post_meta($order_id, 'smartpack_wms_state', 'pending');
            update_post_meta($order_id, 'smartpack_wms_order_state', 'pending');
            update_post_meta($order_id, 'smartpack_wc_order_state', 'processing');
            update_post_meta($order_id, 'smartpack_wms_changed', new DateTime());
        });

        add_action('woocommerce_order_status_completed', function ($order_id) {
            // completed
        });

        add_action('woocommerce_order_status_refunded', function ($order_id) {
            // refunded
        });

        add_action('woocommerce_order_status_cancelled', function ($order_id) {
            // cancelled
        });
    }
}
