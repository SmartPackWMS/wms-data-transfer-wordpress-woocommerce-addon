<?php
namespace SmartPack\WMS\Controllers;

use SmartPack\WMS\Controllers\CLI\CLI_Products;
use SmartPack\WMS\Controllers\CLI\CLI_Orders;
use DateTime;

class WPInit_Controller
{
    function wmsCronProductHook() {
        $product_cli = new CLI_Products();
        $product_cli->execute();
    }
    
    function wmsCronOrderHook() {
        $order_cli = new CLI_Orders();
        $order_cli->execute();
    }

    private function __crontabEventsProductHook()
    {
        add_action('wms_cron_product_hook', [ $this, 'wmsCronProductHook' ]);

        register_deactivation_hook(__FILE__, function () {
            $timestamp = wp_next_scheduled('wms_cron_product_hook');
            wp_unschedule_event($timestamp, 'wms_cron_product_hook');

            wp_clear_scheduled_hook('wms_cron_product_hook');
        });

        if (!wp_next_scheduled('wms_cron_product_hook')) {
            wp_schedule_event(time(), 'per_minute', 'wms_cron_product_hook');
        }
    }

    private function __crontabEventsOrderHook()
    {
        add_action('wms_cron_order_hook',  [ $this, 'wmsCronOrderHook' ]);

        register_deactivation_hook(__FILE__, function () {
            $timestamp = wp_next_scheduled('wms_cron_order_hook');
            wp_unschedule_event($timestamp, 'wms_cron_order_hook');

            wp_clear_scheduled_hook('wms_cron_order_hook');
        });

        if (!wp_next_scheduled('wms_cron_order_hook')) {
            wp_schedule_event(time(), 'per_minute', 'wms_cron_order_hook');
        }
    }

    private function __crontabEvents()
    {
        add_filter('cron_schedules', function ($schedules) {
            $schedules['per_minute'] = array(
                'interval' => 60,
                'display' => 'One Minute'
            );

            return $schedules;
        });

        $this->__crontabEventsProductHook();
        $this->__crontabEventsOrderHook();
    }

    private function __woocommerceProductActions()
    {
        // Product changed
        add_action('woocommerce_update_product', function ($post_id) {
            update_post_meta($post_id, 'smartpack_wms_state', 'pending');
            update_post_meta($post_id, 'smartpack_wms_changed', new DateTime());
        }, 10, 1);
    }

    private function __woocommerceOrderActions()
    {
        // Order Status Change
        add_action('woocommerce_order_status_pending', function ($order_id) {
            // pending
            update_post_meta($order_id, 'smartpack_wms_state', 'pending');
            update_post_meta($order_id, 'smartpack_wms_changed', new DateTime());
        });
        add_action('woocommerce_order_status_failed', function ($order_id) {
            // failed
            update_post_meta($order_id, 'smartpack_wms_state', 'pending');
            update_post_meta($order_id, 'smartpack_wms_changed', new DateTime());
        });

        add_action('woocommerce_order_status_on-hold', function ($order_id) {
            // on-hold
            update_post_meta($order_id, 'smartpack_wms_state', 'pending');
            update_post_meta($order_id, 'smartpack_wms_changed', new DateTime());
        });

        add_action('woocommerce_order_status_processing', function ($order_id) {
            // processing
            update_post_meta($order_id, 'smartpack_wms_state', 'pending');
            update_post_meta($order_id, 'smartpack_wms_changed', new DateTime());
        });

        add_action('woocommerce_order_status_completed', function ($order_id) {
            // completed
            update_post_meta($order_id, 'smartpack_wms_state', 'pending');
            update_post_meta($order_id, 'smartpack_wms_changed', new DateTime());
        });

        add_action('woocommerce_order_status_refunded', function ($order_id) {
            // refunded
            update_post_meta($order_id, 'smartpack_wms_state', 'pending');
            update_post_meta($order_id, 'smartpack_wms_changed', new DateTime());
        });

        add_action('woocommerce_order_status_cancelled', function ($order_id) {
            // cancelled
            update_post_meta($order_id, 'smartpack_wms_state', 'pending');
            update_post_meta($order_id, 'smartpack_wms_changed', new DateTime());
        });
    }

    function init()
    {
        add_action(
            'init',
            function () {
                $this->__woocommerceProductActions();
                $this->__woocommerceOrderActions();
                $this->__crontabEvents();
            }
        );
    }
}
